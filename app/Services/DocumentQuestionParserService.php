<?php

namespace App\Services;

use Exception;
use ZipArchive;

class DocumentQuestionParserService
{
    /**
     * Ekstraksi teks dari file dokumen yang diunggah (.docx, .pdf, .txt, .md).
     */
    public function extractTextFromFile(string $filePath, string $extension): string
    {
        $extension = strtolower($extension);

        if ($extension === 'docx') {
            return $this->extractFromDocx($filePath);
        }

        if ($extension === 'pdf') {
            return $this->extractFromPdf($filePath);
        }

        if ($extension === 'txt' || $extension === 'md') {
            return file_get_contents($filePath) ?: '';
        }

        throw new Exception("Format file .{$extension} tidak didukung. Harap gunakan file Word (.docx), PDF (.pdf), teks (.txt), atau Markdown (.md).");
    }

    /**
     * Ekstraksi teks dari file Word (.docx) berbasis XML internal (ZipArchive),
     * lengkap dengan ekstraksi tabel (Markdown) dan gambar embedded.
     */
    /**
     * Ekstraksi teks dari file Word (.docx) berbasis XML internal (ZipArchive),
     * lengkap dengan rekonstruksi numbering list otomatis, ekstraksi tabel (Markdown), dan gambar embedded.
     */
    public function extractFromDocx(string $filePath): string
    {
        if (!class_exists('ZipArchive')) {
            throw new Exception("Ekstensi PHP ZipArchive tidak aktif pada server.");
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new Exception("Gagal membuka file Word (.docx). Pastikan file tidak rusak atau terproteksi kata sandi.");
        }

        // 1. Ekstraksi relasi gambar dari word/_rels/document.xml.rels
        $imageMap = [];
        if (($relsIndex = $zip->locateName('word/_rels/document.xml.rels')) !== false) {
            $relsXml = $zip->getFromIndex($relsIndex);
            if ($relsXml && preg_match_all('/<Relationship\s+[^>]*Id="([^"]+)"[^>]*Type="[^"]*image"[^>]*Target="([^"]+)"/i', $relsXml, $relMatches, PREG_SET_ORDER)) {
                $storageDir = public_path('storage/questions');
                if (!is_dir($storageDir)) {
                    @mkdir($storageDir, 0777, true);
                }

                foreach ($relMatches as $m) {
                    $rId = $m[1];
                    $target = $m[2];
                    $zipPath = (str_starts_with($target, 'media/') || str_starts_with($target, 'word/'))
                        ? (str_starts_with($target, 'media/') ? 'word/' . $target : $target)
                        : 'word/' . ltrim($target, '/');

                    if ($zip->locateName($zipPath) !== false) {
                        $imgData = $zip->getFromName($zipPath);
                        if ($imgData) {
                            $ext = pathinfo($zipPath, PATHINFO_EXTENSION) ?: 'png';
                            $filename = 'docx_img_' . uniqid() . '.' . $ext;
                            file_put_contents($storageDir . '/' . $filename, $imgData);
                            $imageMap[$rId] = '/storage/questions/' . $filename;
                        }
                    }
                }
            }
        }

        // 2. Ekstraksi format list numbering dari word/numbering.xml
        $numberingMap = [];
        if (($numIndex = $zip->locateName('word/numbering.xml')) !== false) {
            $numXml = $zip->getFromIndex($numIndex);
            if ($numXml) {
                $abstractFmts = [];
                if (preg_match_all('/<w:abstractNum\b[^>]*w:abstractNumId="(\d+)"[^>]*>(.*?)<\/w:abstractNum>/s', $numXml, $absMatches, PREG_SET_ORDER)) {
                    foreach ($absMatches as $abs) {
                        $absId = $abs[1];
                        $absContent = $abs[2];
                        $fmt = 'decimal';
                        if (preg_match('/<w:numFmt\b[^>]*w:val="([^"]+)"/', $absContent, $fmtMatch)) {
                            $fmt = $fmtMatch[1];
                        }
                        $abstractFmts[$absId] = $fmt;
                    }
                }

                if (preg_match_all('/<w:num\b[^>]*w:numId="(\d+)"[^>]*>.*?<w:abstractNumId\b[^>]*w:val="(\d+)"/s', $numXml, $numMatches, PREG_SET_ORDER)) {
                    foreach ($numMatches as $nm) {
                        $numId = $nm[1];
                        $absId = $nm[2];
                        $fmt = $abstractFmts[$absId] ?? 'decimal';
                        $numberingMap[$numId] = [
                            'fmt' => $fmt,
                            'count' => 0,
                        ];
                    }
                }
            }
        }

        // 3. Baca word/document.xml
        $content = '';
        if (($index = $zip->locateName('word/document.xml')) !== false) {
            $xmlData = $zip->getFromIndex($index);
            $zip->close();

            if ($xmlData) {
                // Ganti drawing / blip gambar dengan Markdown image
                foreach ($imageMap as $rId => $imgUrl) {
                    $xmlData = preg_replace(
                        '/<w:drawing[^>]*>.*?r:(?:embed|id)="' . preg_quote($rId, '/') . '".*?<\/w:drawing>/s',
                        "\n\n![Gambar]({$imgUrl})\n\n",
                        $xmlData
                    );
                    $xmlData = preg_replace(
                        '/<w:pict[^>]*>.*?r:(?:embed|id)="' . preg_quote($rId, '/') . '".*?<\/w:pict>/s',
                        "\n\n![Gambar]({$imgUrl})\n\n",
                        $xmlData
                    );
                }

                // Konversi tabel <w:tbl>...</w:tbl> menjadi format Markdown table
                $xmlData = preg_replace_callback('/<w:tbl[^>]*>(.*?)<\/w:tbl>/s', function ($tblMatches) {
                    $tblInner = $tblMatches[1];
                    if (!preg_match_all('/<w:tr[^>]*>(.*?)<\/w:tr>/s', $tblInner, $rowMatches)) {
                        return '';
                    }

                    $mdRows = [];
                    $headerColsCount = 0;
                    $isFirst = true;

                    foreach ($rowMatches[1] as $trXml) {
                        if (!preg_match_all('/<w:tc[^>]*>(.*?)<\/w:tc>/s', $trXml, $cellMatches)) {
                            continue;
                        }

                        $cells = [];
                        foreach ($cellMatches[1] as $tcXml) {
                            $cellText = strip_tags(preg_replace('/<\/w:p>/', ' ', $tcXml));
                            $cellText = trim(preg_replace('/\s+/', ' ', $cellText));
                            $cells[] = str_replace('|', '&#124;', $cellText);
                        }

                        if (count($cells) > 0) {
                            $mdRows[] = '| ' . implode(' | ', $cells) . ' |';
                            if ($isFirst) {
                                $headerColsCount = count($cells);
                                $isFirst = false;
                            }
                        }
                    }

                    if (count($mdRows) === 0) {
                        return '';
                    }

                    // Sisipkan baris separator header
                    $separator = '|' . str_repeat('---|', max(1, $headerColsCount));
                    if (count($mdRows) >= 1) {
                        array_splice($mdRows, 1, 0, [$separator]);
                    }

                    return "\n\n" . implode("\n", $mdRows) . "\n\n";
                }, $xmlData);

                // Rekonstruksi prefix numbering list Word (<w:numPr>) pada setiap paragraf <w:p>
                $xmlData = preg_replace_callback('/<w:p\b[^>]*>(.*?)<\/w:p>/s', function ($pMatch) use (&$numberingMap) {
                    $pXml = $pMatch[1];
                    $prefix = '';
                    if (preg_match('/<w:numPr>.*?<w:numId\b[^>]*w:val="(\d+)".*?<\/w:numPr>/s', $pXml, $numMatch)) {
                        $numId = $numMatch[1];
                        if (isset($numberingMap[$numId])) {
                            $numberingMap[$numId]['count']++;
                            $count = $numberingMap[$numId]['count'];
                            $fmt = $numberingMap[$numId]['fmt'];
                            if ($fmt === 'upperLetter') {
                                $letter = chr(65 + ($count - 1));
                                $prefix = "{$letter}. ";
                            } else {
                                $prefix = "{$count}. ";
                            }
                        }
                    }
                    return "\n" . $prefix . $pXml . "\n";
                }, $xmlData);

                $xmlData = preg_replace('/<w:tab\/>/', "\t", $xmlData);

                // Hilangkan semua tag XML lainnya
                $text = strip_tags($xmlData);
                // Bersihkan entitas HTML
                $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                return trim($text);
            }
        } else {
            $zip->close();
        }

        return $content;
    }

    /**
     * Ekstraksi teks dari file PDF (.pdf) berbasis pembacaan stream teks dasar.
     */
    public function extractFromPdf(string $filePath): string
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return '';
        }

        $text = '';
        // Cari stream teks PDF standar
        if (preg_match_all('/\((.*?)\)\s*T[jJ]/s', $content, $matches)) {
            $text = implode("\n", $matches[1]);
        } elseif (preg_match_all('/\[(.*?)\]\s*TJ/s', $content, $matches)) {
            $extracted = [];
            foreach ($matches[1] as $match) {
                if (preg_match_all('/\((.*?)\)/s', $match, $subMatches)) {
                    $extracted[] = implode('', $subMatches[1]);
                }
            }
            $text = implode("\n", $extracted);
        } else {
            // Fallback: hapus karakter non-printable biner
            $text = preg_replace('/[^\x20-\x7E\r\n\t]/', '', $content);
        }

        return trim($text);
    }

    /**
     * Parsing teks naskah soal menjadi array struktur butir soal terstandarisasi.
     * 
     * @return array<int, array{
     *     question_text: string,
     *     question_type: string,
     *     options: array<int, string>,
     *     correct_option: int,
     *     correct_tf: string,
     *     pairs: array<int, array{premise: string, match: string}>
     * }>
     */
    public function parseQuestionsFromText(string $rawText): array
    {
        $text = trim($rawText);
        if ($text === '') {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $text);
        $blocks = [];
        $currentBlock = [];
        $hasStartedFirstQuestion = false;

        // Cek apakah dokumen menggunakan penomoran soal (1. / Soal 1)
        $hasNumberedQuestions = false;
        foreach ($lines as $rawLine) {
            $tr = trim($rawLine);
            if (preg_match('/^(\d+[\.\)]|\bsoal\s*\d+[\.\:]?)/i', $tr)) {
                $hasNumberedQuestions = true;
                break;
            }
        }

        $lastLineWasKey = false;

        foreach ($lines as $rawLine) {
            $line = trim($rawLine);
            if ($line === '') {
                continue;
            }

            $isNumbered = preg_match('/^(\d+[\.\)]|\bsoal\s*\d+[\.\:]?)/i', $line);
            $isKeyLine = preg_match('/^(?:[\s\-\*\+\•\>\#]*)(?:kunci|jawaban|kunci\s*jawaban|key|ans)\s*[\:\=]?\s*(benar|salah|true|false|[A-Ea-e]\b)/i', $line);

            // Jika nomor soal baru terdeteksi ATAU baris sebelumnya adalah Kunci dan sekarang baris teks baru
            $isNewQuestion = $isNumbered || ($lastLineWasKey && !$isKeyLine && count($currentBlock) > 0);

            if ($isNewQuestion) {
                if (count($currentBlock) > 0) {
                    $blocks[] = $currentBlock;
                }
                $currentBlock = [$line];
                $hasStartedFirstQuestion = true;
                $lastLineWasKey = false;
            } elseif ($hasStartedFirstQuestion || !$hasNumberedQuestions) {
                $currentBlock[] = $line;
            }

            if ($isKeyLine) {
                $lastLineWasKey = true;
            }
        }

        if (count($currentBlock) > 0) {
            $blocks[] = $currentBlock;
        }

        $parsedQuestions = [];

        foreach ($blocks as $block) {
            $qText = '';
            $qType = 'multiple_choice';
            $options = [];
            $correctOpt = 0;
            $correctTf = 'Benar';
            $pairs = [];

            $inOptions = false;
            $hasExplicitKey = false;

            // Pass 1: Identifikasi apakah blok memiliki opsi A-E atau kunci eksplisit
            $hasOptions = false;
            foreach ($block as $l) {
                $cleanL = preg_replace('/^[\s\-\*\+\•\>\#]+\s*/u', '', trim($l));
                $normL = preg_replace('/(?<=[a-z0-9\)\”\”\'\.\,\;\:\!\?])(?=[B-Eb-e]\.\s*)/u', ' ', $cleanL);
                if (preg_match('/^([A-Ea-e])[\.\)]\s*(.*)$/', $normL)) {
                    $hasOptions = true;
                }
                if (preg_match('/^(?:kunci|jawaban|kunci\s*jawaban|key|ans)\s*[\:\=]?\s*(benar|salah|true|false|[A-Ea-e]\b)/i', $cleanL)) {
                    $hasExplicitKey = true;
                }
            }

            foreach ($block as $rawLine) {
                $line = trim($rawLine);
                if ($line === '') {
                    continue;
                }

                $isTableOrImageLine = str_starts_with($line, '|') || str_starts_with($line, '!') || str_starts_with($line, '<img') || str_starts_with($line, '<table');

                // Hilangkan bullet markdown list seperti "- ", "* ", "• " di awal baris
                $cleanLine = preg_replace('/^[\s\-\*\+\•\>\#]+\s*/u', '', $line);

                $keyMatch = [];
                $pairMatch = [];

                // 1. Cek Kunci Jawaban di baris mandiri
                if (preg_match('/^(?:kunci|jawaban|kunci\s*jawaban|key|ans)\s*[\:\=]?\s*(benar|salah|true|false|[A-Ea-e]\b)/i', $cleanLine, $keyMatch)) {
                    $ans = strtolower(trim($keyMatch[1]));
                    if ($ans === 'benar' || $ans === 'true') {
                        $qType = 'true_false';
                        $correctTf = 'Benar';
                    } elseif ($ans === 'salah' || $ans === 'false') {
                        $qType = 'true_false';
                        $correctTf = 'Salah';
                    } else {
                        $letterCode = ord(strtoupper($ans)) - 65;
                        if ($letterCode >= 0 && $letterCode <= 4) {
                            $correctOpt = $letterCode;
                        }
                    }
                    continue;
                }

                // Normalisasi opsi yang menempel tanpa spasi (misal: "diperbaruiB. tidak")
                $normalizedLine = preg_replace('/(?<=[a-z0-9\)\”\”\'\.\,\;\:\!\?])(?=[B-Eb-e]\.\s*)/u', ' ', $cleanLine);

                // 2. Cek apakah baris merupakan opsi (misal: "A. Opsi", "- A. Opsi", atau "A. Opsi 1 B. Opsi 2")
                if (!$isTableOrImageLine && preg_match('/^([A-Ea-e])[\.\)]\s*(.*)$/', $normalizedLine)) {
                    $inOptions = true;

                    // Pisahkan jika ada opsi ganda dalam satu baris (contoh: "A. teks A B. teks B")
                    $inlineParts = preg_split('/(?=(?:^|\s+)[B-Eb-e][\.\)]\s*)/', $normalizedLine, -1, PREG_SPLIT_NO_EMPTY);

                    foreach ($inlineParts as $part) {
                        $cleanPart = preg_replace('/^[\s\-\*\+\•\>\#]+\s*/u', '', trim($part));
                        if (preg_match('/^([A-Ea-e])[\.\)]\s*(.*)$/', $cleanPart, $optSubMatch)) {
                            $optLetter = strtoupper($optSubMatch[1]);
                            $optContent = trim($optSubMatch[2]);

                            // Cek jika ada trailing "Kunci X" yang menempel di akhir kalimat opsi
                            if (preg_match('/[\s,;]+(?:kunci|jawaban|kunci\s*jawaban|key|ans)\s*[\:\=]?\s*([A-Ea-e]|benar|salah|true|false)\s*$/i', $optContent, $trailingKeyMatch)) {
                                $trailingAns = strtolower(trim($trailingKeyMatch[1]));
                                if ($trailingAns === 'benar' || $trailingAns === 'true') {
                                    $qType = 'true_false';
                                    $correctTf = 'Benar';
                                } elseif ($trailingAns === 'salah' || $trailingAns === 'false') {
                                    $qType = 'true_false';
                                    $correctTf = 'Salah';
                                } else {
                                    $letterCode = ord(strtoupper($trailingAns)) - 65;
                                    if ($letterCode >= 0 && $letterCode <= 4) {
                                        $correctOpt = $letterCode;
                                    }
                                }
                                $optContent = trim(preg_replace('/[\s,;]+(?:kunci|jawaban|kunci\s*jawaban|key|ans)\s*[\:\=]?\s*([A-Ea-e]|benar|salah|true|false)\s*$/i', '', $optContent));
                            }

                            $options[] = $optContent;
                        }
                    }
                    continue;
                }

                // 3. Cek format Menjodohkan (Matching Pair: "Premis = Jawaban" atau "Premis -> Jawaban")
                if (!$inOptions && !$hasOptions && !$isTableOrImageLine && preg_match('/^(.{1,60}?)\s*(?:=|->|—)\s*(.{1,60})$/', $cleanLine, $pairMatch) && !preg_match('/^(\d+[\.\)]|\bsoal|\bperhatikan|\bberapakah|\btentukan|\bjika|\bhitung|\bapakah)/i', $cleanLine) && !str_ends_with($cleanLine, '?')) {
                    $pairs[] = [
                        'premise' => trim($pairMatch[1]),
                        'match' => trim($pairMatch[2]),
                    ];
                    continue;
                }

                // 4. Baris teks pertanyaan soal
                if (!$inOptions) {
                    $cleanQText = preg_replace('/^(\d+[\.\)]|\bsoal\s*\d+[\.\:]?)\s*/i', '', $line);
                    $qText .= ($qText !== '' ? "\n" : '') . $cleanQText;
                }
            }

            // Tentukan tipe soal
            if (count($pairs) >= 2 && !$hasOptions) {
                $qType = 'matching';
            } elseif ($qType === 'true_false') {
                // Tetap true_false
            } elseif (count($options) >= 2 || $hasOptions) {
                $qType = 'multiple_choice';
            }

            // Pastikan minimal 4 slot opsi untuk kestabilan UI form builder jika multiple choice
            if ($qType === 'multiple_choice') {
                while (count($options) < 4) {
                    $options[] = '';
                }
            }

            // Hindari memasukkan blok yang hanya berupa judul template tanpa isi soal
            if ($qText === '' && count($pairs) === 0 && empty($options[0])) {
                continue;
            }

            $parsedQuestions[] = [
                'question_text' => $qText ?: 'Pertanyaan Soal',
                'question_type' => $qType,
                'options' => $options,
                'correct_option' => $correctOpt,
                'correct_tf' => $correctTf,
                'pairs' => count($pairs) >= 2 ? $pairs : [
                    ['premise' => '', 'match' => ''],
                    ['premise' => '', 'match' => ''],
                ],
            ];
        }

        return $parsedQuestions;
    }

    /**
     * Menghasilkan file template format naskah soal Word (.docx) lengkap dengan contoh Gambar dan Tabel.
     */
    public function generateDocxTemplate(): string
    {
        $existingTemplate = base_path('Template_Format_Soal_LMS.docx');
        if (file_exists($existingTemplate)) {
            $tempFile = tempnam(sys_get_temp_dir(), 'tpl_docx_') . '.docx';
            copy($existingTemplate, $tempFile);
            return $tempFile;
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'tpl_docx_') . '.docx';
        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Gagal membuat file template Word sementara.");
        }

        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
</Types>';

        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>';

        $lines = [
            'FORMAT PENULISAN NASKAH SOAL KUIS (RUANGTERRA LMS)',
            'Panduan: Tuliskan setiap nomor soal secara berurutan dengan format berikut.',
            '',
            '1. Perhatikan gambar diagram di bawah ini!',
            'Berdasarkan teorema Pythagoras, berapakah panjang sisi miring (c) jika diketahui panjang sisi a = 6 cm dan b = 8 cm?',
            'A. 10 cm',
            'B. 12 cm',
            'C. 14 cm',
            'D. 16 cm',
            'Kunci: A',
            '',
            '2. Perhatikan tabel data penjualan buku di toko literasi berikut:',
            '| Hari | Jumlah Terjual |',
            '|---|---|',
            '| Senin | 15 |',
            '| Selasa | 20 |',
            '| Rabu | 25 |',
            'Berapakah total penjualan buku dari hari Senin sampai Rabu?',
            'A. 50',
            'B. 60',
            'C. 70',
            'D. 80',
            'Kunci: B',
            '',
            '3. Apa ibukota negara Indonesia saat ini?',
            'A. Jakarta',
            'B. Bandung',
            'C. Surabaya',
            'D. Medan',
            'Kunci: A',
            '',
            '4. Bumi mengelilingi matahari dalam kurun waktu satu tahun penuh.',
            'Kunci: Benar',
            '',
            '5. Logam merkuri berwujud padat pada suhu ruangan kamar.',
            'Kunci: Salah',
            '',
            '6. Jodohkan bahasa pemrograman berikut dengan ekstensinya:',
            'PHP = .php',
            'Python = .py',
            'JavaScript = .js',
            'CSS = .css',
            '',
        ];

        $docXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:body>';

        foreach ($lines as $line) {
            $safeText = htmlspecialchars($line, ENT_QUOTES | ENT_XML1, 'UTF-8');
            $docXml .= '<w:p><w:r><w:t xml:space="preserve">' . $safeText . '</w:t></w:r></w:p>';
        }

        $docXml .= '  </w:body>
</w:document>';

        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rels);
        $zip->addFromString('word/document.xml', $docXml);
        $zip->close();

        return $tempFile;
    }

    /**
     * Menghasilkan teks template format naskah soal (.txt).
     */
    public function getTextTemplateContent(): string
    {
        return "FORMAT PENULISAN NASKAH SOAL KUIS (RUANGTERRA LMS)\n"
            . "Panduan: Tuliskan setiap nomor soal secara berurutan dengan format berikut.\n\n"
            . "1. Perhatikan gambar diagram segitiga berikut:\n"
            . "![Diagram Segitiga](/storage/questions/sample_segitiga.png)\n"
            . "Berdasarkan teorema Pythagoras, berapakah panjang sisi miring (c) jika diketahui panjang sisi a = 6 cm dan b = 8 cm?\n"
            . "A. 10 cm\n"
            . "B. 12 cm\n"
            . "C. 14 cm\n"
            . "D. 16 cm\n"
            . "Kunci: A\n\n"
            . "2. Perhatikan tabel data penjualan buku berikut:\n"
            . "| Hari | Jumlah Terjual |\n"
            . "|---|---|\n"
            . "| Senin | 15 |\n"
            . "| Selasa | 20 |\n"
            . "| Rabu | 25 |\n"
            . "Berapakah total penjualan buku dari hari Senin sampai Rabu?\n"
            . "A. 50\n"
            . "B. 60\n"
            . "C. 70\n"
            . "D. 80\n"
            . "Kunci: B\n\n"
            . "3. Apa ibukota negara Indonesia saat ini?\n"
            . "A. Jakarta\n"
            . "B. Bandung\n"
            . "C. Surabaya\n"
            . "D. Medan\n"
            . "Kunci: A\n\n"
            . "4. Bumi mengelilingi matahari dalam kurun waktu satu tahun penuh (revolusi bumi).\n"
            . "Kunci: Benar\n\n"
            . "5. Logam merkuri (raksa) berwujud padat pada suhu ruangan kamar normal.\n"
            . "Kunci: Salah\n\n"
            . "6. Jodohkan bahasa pemrograman berikut dengan ekstensinya:\n"
            . "PHP = .php\n"
            . "Python = .py\n"
            . "JavaScript = .js\n"
            . "CSS = .css\n";
    }
}
