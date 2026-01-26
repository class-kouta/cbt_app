<?php

namespace App\Application\Service;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * CSV出力共通サービス
 * iOS/Androidでも動作するUTF-8 BOM付きCSVを生成
 */
class CsvExportService
{
    /**
     * CSVをストリームレスポンスとして出力
     *
     * @param array<string> $headers CSVヘッダー行
     * @param array<int, array<int, mixed>> $rows CSVデータ行
     * @param string $filename ファイル名
     * @return StreamedResponse
     */
    public function export(array $headers, array $rows, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM（Excel対応）
            fwrite($handle, "\xEF\xBB\xBF");

            // ヘッダー行
            fputcsv($handle, $headers);

            // データ行
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * 日時を日本語形式にフォーマット
     */
    public function formatDatetime(?string $datetime): string
    {
        if (empty($datetime)) {
            return '';
        }

        return date('Y/m/d H:i', strtotime($datetime));
    }

    /**
     * 配列をカンマ区切り文字列に変換
     *
     * @param array<mixed>|null $items
     */
    public function joinArray(?array $items, string $key = null): string
    {
        if (empty($items)) {
            return '';
        }

        if ($key !== null) {
            return implode(', ', array_column($items, $key));
        }

        return implode(', ', $items);
    }

    /**
     * ファイル名用の日付文字列を生成
     */
    public function getDateSuffix(): string
    {
        return now()->format('Ymd');
    }
}
