<?php

namespace Tests\Feature;

use App\GrnProduct;
use App\Product;
use App\ProductWarrantySerial;
use App\User;
use Tests\TestCase;

class ProductWarrantyViewTest extends TestCase
{
    public function test_grn_product_summary_shows_unit_warranty_and_document()
    {
        $product = new GrnProduct;
        $product->setRelation('products', new Product(['warranty_period' => 36]));
        $unit = new ProductWarrantySerial([
            'serial_number' => 'SUMMARY-SERIAL', 'warranty_number' => 'CARD-01',
            'warranty_period' => 12, 'warranty_document' => 'assets/documents/warranty/card.pdf',
            'additional_warranty_description' => '<script>terms</script>',
        ]);
        $product->setRelation('productWarrantySerial', collect([$unit]));
        $html = view('grn.warranty-summary', compact('product'))->render();
        foreach (['SUMMARY-SERIAL', 'CARD-01', '12 Months', 'card.pdf', 'View Document', 'Download', '&lt;script&gt;terms&lt;/script&gt;'] as $text) {
            $this->assertStringContainsString($text, $html);
        }
        $unit->warranty_period = null;
        $unit->warranty_document = null;
        $html = view('grn.warranty-summary', compact('product'))->render();
        $this->assertStringContainsString('36 Months', $html);
        $this->assertStringContainsString('No document uploaded', $html);
    }

    public function test_grn_values_populate_serial_and_three_warranty_fields_when_master_is_empty()
    {
        $product = new Product;
        $unit = new ProductWarrantySerial([
            'serial_number' => '00001', 'warranty_period' => 12,
            'warranty_document' => 'assets/documents/warranty/card.jpg',
            'additional_warranty_description' => 'Compressor warranty',
        ]);
        $data = ['product' => $product, 'warrantyUnits' => collect([$unit])];
        $serial = view('product.serial-values', $data)->render();
        $this->assertStringContainsString('00001', $serial);
        $this->assertStringNotContainsString('Not recorded', $serial);
        $html = view('product.warranty-fields', $data)->render();
        foreach (['Warranty Period', 'Warranty Document', 'Additional Warranty Description',
            '12 Months', 'card.jpg', 'Compressor warranty'] as $text) {
            $this->assertStringContainsString($text, $html);
        }
        $this->assertStringNotContainsString('Not recorded', $html);
        $unit2 = new ProductWarrantySerial(['serial_number' => '00002', 'warranty_period' => 24]);
        $data['warrantyUnits']->push($unit2);
        $html = view('product.warranty-fields', $data)->render();
        foreach (['00001:', '00002:', '12 Months', '24 Months'] as $text) {
            $this->assertStringContainsString($text, $html);
        }
    }

    public function test_unit_warranty_uses_defaults_and_displays_unit_overrides()
    {
        $user = new class extends User {
            public function GrnNumberFormat($number) { return 'GRN' . $number; }
        };
        $this->actingAs($user);
        $product = new Product;
        $product->warranty_period = 36;
        $product->additional_warranty_description = 'Default warranty';
        $line = new GrnProduct;
        $line->grn_id = 1;
        $unit = new ProductWarrantySerial([
            'serial_number' => 'TEST-SERIAL', 'warranty_number' => 'TEST-WARRANTY', 'status' => 0,
        ]);
        $unit->setRelation('product', $line);
        $data = ['product' => $product, 'warrantyUnits' => collect([$unit])];
        $html = view('product.warranty-units', $data)->render();
        foreach (['TEST-SERIAL', 'TEST-WARRANTY', '36 Months', 'Default warranty'] as $text) {
            $this->assertStringContainsString($text, $html);
        }
        $unit->warranty_period = 12;
        $unit->warranty_document = 'assets/documents/warranty/test.pdf';
        $unit->additional_warranty_description = '<script>unsafe</script>';
        $html = view('product.warranty-units', $data)->render();
        foreach (['12 Months', 'View document', '&lt;script&gt;unsafe&lt;/script&gt;'] as $text) {
            $this->assertStringContainsString($text, $html);
        }
        $this->assertStringNotContainsString('<script>unsafe</script>', $html);
    }

    public function test_missing_unit_records_show_an_empty_state()
    {
        $html = view('product.warranty-units', [
            'product' => new Product, 'warrantyUnits' => collect(),
        ])->render();
        $this->assertStringContainsString('No serial or warranty details recorded', $html);
    }
}
