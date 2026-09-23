/**
 * RuangTerra LMS - Fast Chunked File Uploader (Vanilla JS)
 * Mengunggah file besar per 2.5MB secara bertahap tanpa timeout server.
 */
class ChunkedUploader {
    constructor(options = {}) {
        // Optimal chunk size 2.5MB: sangat cepat, minim round-trip request, aman di bawah PHP post_max_size
        this.chunkSize = options.chunkSize || Math.floor(2.5 * 1024 * 1024);
        this.uploadUrl = options.uploadUrl || '/admin/upload/chunk';
        this.cancelUrl = options.cancelUrl || '/admin/upload/chunk/cancel';
        this.csrfToken = options.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        this.targetFolder = options.targetFolder || 'material-banks';
        this.onProgress = options.onProgress || function() {};
        this.onSuccess = options.onSuccess || function() {};
        this.onError = options.onError || function() {};
        this.onStart = options.onStart || function() {};
        
        this.isCancelled = false;
        this.fileUuid = null;
    }

    generateUuid() {
        return 'chunk_' + Date.now() + '_' + Math.random().toString(36).substring(2, 11);
    }

    cancel() {
        this.isCancelled = true;
        if (this.fileUuid) {
            const formData = new FormData();
            formData.append('file_uuid', this.fileUuid);
            formData.append('_token', this.csrfToken);
            if (navigator.sendBeacon) {
                navigator.sendBeacon(this.cancelUrl, formData);
            } else {
                fetch(this.cancelUrl, { method: 'POST', body: formData });
            }
        }
    }

    async upload(file) {
        if (!file) return;

        // Validasi ekstensi terlarang (arsip ZIP dan RAR)
        const ext = file.name.split('.').pop().toLowerCase();
        if (['zip', 'rar'].includes(ext)) {
            const err = new Error('Format file arsip (.zip dan .rar) tidak diizinkan. Silakan unggah dokumen PDF, DOCX, PPTX, XLSX, TXT, atau gambar.');
            this.onError(err);
            return;
        }

        this.isCancelled = false;
        this.fileUuid = this.generateUuid();
        const totalSize = file.size;
        const totalChunks = Math.ceil(totalSize / this.chunkSize) || 1;

        this.onStart({ file, totalChunks, totalSize });

        let uploadedBytes = 0;

        for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
            if (this.isCancelled) {
                this.onError(new Error('Unggah file dibatalkan oleh pengguna.'));
                return;
            }

            const start = chunkIndex * this.chunkSize;
            const end = Math.min(start + this.chunkSize, totalSize);
            const chunkBlob = file.slice(start, end);

            const formData = new FormData();
            formData.append('file', chunkBlob, file.name);
            formData.append('chunk_index', chunkIndex);
            formData.append('total_chunks', totalChunks);
            formData.append('file_uuid', this.fileUuid);
            formData.append('original_filename', file.name);
            formData.append('target_folder', this.targetFolder);
            formData.append('_token', this.csrfToken);

            try {
                const response = await fetch(this.uploadUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    const errMsg = data.message || `Gagal mengunggah bagian file (${chunkIndex + 1}/${totalChunks})`;
                    this.onError(new Error(errMsg));
                    return;
                }

                uploadedBytes += (end - start);
                const percent = Math.min(100, Math.round((uploadedBytes / totalSize) * 100));

                this.onProgress({
                    percent,
                    uploadedBytes,
                    totalBytes: totalSize,
                    chunkIndex: chunkIndex + 1,
                    totalChunks,
                    completed: data.completed || false
                });

                if (data.completed) {
                    this.onSuccess(data);
                    return data;
                }
            } catch (err) {
                this.onError(new Error('Koneksi terputus saat mengunggah file: ' + err.message));
                return;
            }
        }
    }
}

/**
 * Helper UI Otomatis untuk mengikat ChunkedUploader ke elemen input file
 */
function initChunkedFileInput(config) {
    const fileInput = typeof config.input === 'string' ? document.querySelector(config.input) : config.input;
    if (!fileInput) return null;

    const originalInputName = fileInput.getAttribute('name') || 'document_file';
    const container = config.container ? (typeof config.container === 'string' ? document.querySelector(config.container) : config.container) : fileInput.parentElement;
    const hiddenInputName = config.hiddenInputName || 'document_chunk_path';
    const originalNameInputName = config.originalNameInputName || 'original_filename';
    const submitButtons = config.submitButtons ? document.querySelectorAll(config.submitButtons) : document.querySelectorAll('button[type="submit"]');
    const targetFolder = config.targetFolder || 'material-banks';
    const maxSizeBytes = config.maxSizeBytes || (100 * 1024 * 1024); // default 100MB

    // Siapkan hidden input untuk menyimpan path chunk file yang sudah dirangkai
    let hiddenInput = container.querySelector(`input[name="${hiddenInputName}"]`);
    if (!hiddenInput) {
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = hiddenInputName;
        container.appendChild(hiddenInput);
    }

    let origNameInput = container.querySelector(`input[name="${originalNameInputName}"]`);
    if (!origNameInput) {
        origNameInput = document.createElement('input');
        origNameInput.type = 'hidden';
        origNameInput.name = originalNameInputName;
        container.appendChild(origNameInput);
    }

    // Hindari duplikasi pengiriman file besar saat submit form (Mencegah ERR_CONNECTION_RESET / site can't be reached)
    const parentForm = fileInput.closest('form');
    if (parentForm && !parentForm.dataset.chunkFormBound) {
        parentForm.dataset.chunkFormBound = 'true';
        parentForm.addEventListener('submit', function() {
            if (hiddenInput && hiddenInput.value) {
                // Hapus name pada fileInput agar browser TIDAK mengunggah ulang file 15MB via POST form standar
                fileInput.removeAttribute('name');
            }
        });
    }

    // UI Progress Bar Container
    let progressWrapper = container.querySelector('.chunk-progress-wrapper');
    if (!progressWrapper) {
        progressWrapper = document.createElement('div');
        progressWrapper.className = 'chunk-progress-wrapper mt-2 d-none';
        progressWrapper.innerHTML = `
            <div class="p-2 rounded-2" style="background: rgba(32, 107, 196, 0.05); border: 1px dashed rgba(32, 107, 196, 0.3);">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="chunk-filename small fw-semibold text-truncate" style="max-width: 75%; font-size: 0.8rem; color: #206bc4;"></span>
                    <span class="chunk-status-text small fw-bold" style="font-size: 0.78rem; color: #206bc4;">0%</span>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <span class="chunk-size-info small text-muted" style="font-size: 0.72rem;">0 MB / 0 MB</span>
                    <button type="button" class="btn btn-link btn-sm text-danger p-0 chunk-cancel-btn" style="font-size: 0.75rem; text-decoration: none;">
                        <i class="ti ti-x"></i> Batal Unggah
                    </button>
                </div>
            </div>
        `;
        container.appendChild(progressWrapper);
    }

    const filenameEl = progressWrapper.querySelector('.chunk-filename');
    const statusTextEl = progressWrapper.querySelector('.chunk-status-text');
    const progressBarEl = progressWrapper.querySelector('.progress-bar');
    const sizeInfoEl = progressWrapper.querySelector('.chunk-size-info');
    const cancelBtn = progressWrapper.querySelector('.chunk-cancel-btn');

    let currentUploader = null;

    function formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return (bytes / Math.pow(k, i)).toFixed(1) + ' ' + sizes[i];
    }

    function setSubmitDisabled(disabled) {
        submitButtons.forEach(btn => {
            btn.disabled = disabled;
            if (disabled) {
                btn.dataset.prevHtml = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mengunggah File...';
            } else if (btn.dataset.prevHtml) {
                btn.innerHTML = btn.dataset.prevHtml;
                delete btn.dataset.prevHtml;
            }
        });
    }

    cancelBtn.addEventListener('click', function() {
        if (currentUploader) {
            currentUploader.cancel();
            hiddenInput.value = '';
            origNameInput.value = '';
            fileInput.value = '';
            fileInput.setAttribute('name', originalInputName);
            progressWrapper.classList.add('d-none');
            setSubmitDisabled(false);
        }
    });

    fileInput.addEventListener('change', function() {
        const file = this.files && this.files[0];
        if (!file) return;

        // Cek ekstensi terlarang (ZIP dan RAR)
        const ext = file.name.split('.').pop().toLowerCase();
        if (['zip', 'rar'].includes(ext)) {
            alert('File arsip (.zip dan .rar) tidak diizinkan. Silakan unggah file PDF, DOCX, PPTX, XLSX, TXT, atau gambar.');
            this.value = '';
            hiddenInput.value = '';
            origNameInput.value = '';
            fileInput.setAttribute('name', originalInputName);
            progressWrapper.classList.add('d-none');
            return;
        }

        if (file.size > maxSizeBytes) {
            alert(`Ukuran file melebihi batas maksimal (${formatBytes(maxSizeBytes)}).`);
            this.value = '';
            hiddenInput.value = '';
            origNameInput.value = '';
            fileInput.setAttribute('name', originalInputName);
            progressWrapper.classList.add('d-none');
            return;
        }

        // Tampilkan progress wrapper
        progressWrapper.classList.remove('d-none');
        filenameEl.textContent = file.name;
        statusTextEl.textContent = '0%';
        statusTextEl.className = 'chunk-status-text small fw-bold text-primary';
        progressBarEl.style.width = '0%';
        progressBarEl.className = 'progress-bar progress-bar-striped progress-bar-animated bg-primary';
        sizeInfoEl.textContent = `0 MB / ${formatBytes(file.size)}`;
        hiddenInput.value = '';
        origNameInput.value = file.name;

        setSubmitDisabled(true);

        currentUploader = new ChunkedUploader({
            targetFolder: targetFolder,
            uploadUrl: config.uploadUrl || '/admin/upload/chunk',
            cancelUrl: config.cancelUrl || '/admin/upload/chunk/cancel',
            onProgress: function(progress) {
                progressBarEl.style.width = progress.percent + '%';
                statusTextEl.textContent = progress.percent + '%';
                sizeInfoEl.textContent = `${formatBytes(progress.uploadedBytes)} / ${formatBytes(progress.totalBytes)} (${progress.percent}%)`;
            },
            onSuccess: function(response) {
                progressBarEl.style.width = '100%';
                progressBarEl.className = 'progress-bar bg-success';
                statusTextEl.textContent = 'Siap!';
                statusTextEl.className = 'chunk-status-text small fw-bold text-success';
                sizeInfoEl.textContent = `Selesai diunggah (${formatBytes(response.file_size)})`;
                hiddenInput.value = response.file_path;
                origNameInput.value = response.original_filename;
                
                // File sudah tersimpan di server via chunk -> copot name form submit
                fileInput.removeAttribute('name');
                setSubmitDisabled(false);

                if (typeof config.onSuccess === 'function') {
                    config.onSuccess(response);
                }
            },
            onError: function(err) {
                progressBarEl.className = 'progress-bar bg-danger';
                statusTextEl.textContent = 'Gagal';
                statusTextEl.className = 'chunk-status-text small fw-bold text-danger';
                sizeInfoEl.textContent = err.message;
                hiddenInput.value = '';
                origNameInput.value = '';
                fileInput.setAttribute('name', originalInputName);
                setSubmitDisabled(false);

                if (typeof config.onError === 'function') {
                    config.onError(err);
                }
            }
        });

        currentUploader.upload(file);
    });

    return {
        uploader: currentUploader,
        cancel: () => cancelBtn.click()
    };
}

window.ChunkedUploader = ChunkedUploader;
window.initChunkedFileInput = initChunkedFileInput;
