<?php

namespace Tests\Unit\Domain\Entity;

use App\Domain\Entity\ExposureSession;
use PHPUnit\Framework\TestCase;

class ExposureSessionTest extends TestCase
{
    public function test_should_persist_new_bulk_item_when_suds_after_is_set(): void
    {
        $this->assertTrue(ExposureSession::shouldPersistNewBulkItem(
            reflection: null,
            hierarchyItemId: null,
            sudsAfter: 40
        ));
    }

    public function test_should_persist_new_bulk_item_when_hierarchy_item_is_set(): void
    {
        $this->assertTrue(ExposureSession::shouldPersistNewBulkItem(
            reflection: null,
            hierarchyItemId: 10,
            sudsAfter: null
        ));
    }

    public function test_should_not_persist_new_bulk_item_when_all_fields_are_empty(): void
    {
        $this->assertFalse(ExposureSession::shouldPersistNewBulkItem(
            reflection: null,
            hierarchyItemId: null,
            sudsAfter: null
        ));
    }

    public function test_should_not_persist_new_bulk_item_when_only_whitespace_text_is_set(): void
    {
        $this->assertFalse(ExposureSession::shouldPersistNewBulkItem(
            reflection: '  ',
            hierarchyItemId: null,
            sudsAfter: null
        ));
    }
}
