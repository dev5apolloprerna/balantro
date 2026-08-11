<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class BackNavigationTest extends TestCase
{
    public function test_blade_back_links_do_not_use_browser_history(): void
    {
        $views = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(dirname(__DIR__, 2).'/resources/views')
        );

        foreach ($views as $view) {
            if (! $view->isFile() || ! str_contains($view->getFilename(), '.blade')) {
                continue;
            }

            $contents = file_get_contents($view->getPathname());

            $this->assertDoesNotMatchRegularExpression(
                '/(?:window\.)?history\s*\.\s*(?:back|go)\s*\(/i',
                $contents,
                "Browser-history navigation remains in {$view->getPathname()}"
            );
            $this->assertStringNotContainsString('javascript:history', strtolower($contents));
        }
    }

    #[DataProvider('backLinkProvider')]
    public function test_back_links_have_stable_named_destinations(string $view, string $route): void
    {
        $contents = file_get_contents(dirname(__DIR__, 2).'/'.$view);

        $this->assertStringContainsString("route('{$route}')", $contents);
    }

    public static function backLinkProvider(): array
    {
        return [
            'bulk sales preview' => ['resources/views/admin/bulkupload/sales/preview.blade.php', 'data_entry_operators.bulkuploadsales'],
            'bulk bank preview' => ['resources/views/admin/bulkupload/bank/preview.blade.php', 'data_entry_operators.bulkuploadbankstatement'],
            'credit note preview' => ['resources/views/admin/bulkupload/credit_note/preview.blade.php', 'cn.index'],
            'processing sales preview' => ['resources/views/admin/transaction-processing/sales/preview.blade.php', 'transaction_processing.processing_sales'],
            'processing journal preview' => ['resources/views/admin/transaction-processing/journal/preview.blade.php', 'transaction_processing.processing_journal'],
            'document activities' => ['resources/views/client/documents/activities.blade.php', 'documents.index'],
        ];
    }
}