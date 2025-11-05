<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Supplier;
use App\Models\Quote;
use App\Models\Techpack;
use App\Models\SampleOrder;
use App\Models\ProductionMilestone;
use App\Models\PurchaseOrder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        // Create clients
        $clients = [
            Client::create([
                'name' => 'Fashion Retail Corp',
                'legal_name' => 'Fashion Retail Corp SA',
                'tax_id' => '30-12345678-9',
                'contact' => 'Ana Garcia',
                'email' => 'ana.garcia@fashionretail.com',
                'phone' => '+54 11 5000-1001',
                'whatsapp' => '+54 11 5000-1001',
                'address' => 'Av. Santa Fe 3456, CABA',
                'city' => 'Buenos Aires',
                'country_code' => 'Argentina',
                'country' => 'Argentina',
                'timezone' => 'America/Buenos_Aires',
                'currency' => 'USD',
                'credit_limit' => 50000.00,
                'payment_terms' => '30 días',
                'is_active' => true
            ]),
            Client::create([
                'name' => 'Global Apparel Inc',
                'legal_name' => 'Global Apparel Inc',
                'tax_id' => '33-98765432-1',
                'contact' => 'María Rodriguez',
                'email' => 'maria.rodriguez@globalapparel.com',
                'phone' => '+54 11 5000-3003',
                'whatsapp' => '+54 11 5000-3003',
                'address' => 'Av. Corrientes 2500, CABA',
                'city' => 'Buenos Aires',
                'country_code' => 'Argentina',
                'country' => 'Argentina',
                'timezone' => 'America/Buenos_Aires',
                'currency' => 'USD',
                'credit_limit' => 100000.00,
                'payment_terms' => '30 días',
                'is_active' => true
            ]),
        ];

        // Create suppliers
        $suppliers = [
            Supplier::create([
                'name' => 'Textile Manufacturers Ltd',
                'contact' => 'Juan Pérez',
                'email' => 'supplier@textile.com',
                'phone' => '+1555123456',
                'country' => 'China',
                'city' => 'Shanghai'
            ]),
            Supplier::create([
                'name' => 'Global Fabrics Inc',
                'contact' => 'María González',
                'email' => 'info@globalfabrics.com',
                'phone' => '+1555987654',
                'country' => 'India',
                'city' => 'Mumbai'
            ]),
        ];

        // Create quotes
        $quote1 = Quote::create([
            'code' => '#834893',
            'client_id' => $clients[0]->id,
            'supplier_id' => $suppliers[0]->id,
            'created_date' => now()->subDays(10),
            'delivery_date' => now()->addDays(60),
            'quantity' => 500,
            'unit_price' => 15.75,
            'total_cost' => 7875.00,
            'estimated_cost' => 7000.00,
            'profit_margin' => 12.50,
            'status' => 'in_production',
        ]);

        $quote2 = Quote::create([
            'code' => '#000123',
            'client_id' => $clients[1]->id,
            'supplier_id' => $suppliers[1]->id,
            'created_date' => now()->subDays(5),
            'delivery_date' => now()->addDays(45),
            'quantity' => 1000,
            'unit_price' => 12.50,
            'total_cost' => 12500.00,
            'estimated_cost' => 11000.00,
            'profit_margin' => 13.64,
            'status' => 'pending',
        ]);

        // Create techpacks for clients
        $techpack1 = Techpack::create([
            'client_id' => $clients[0]->id,
            'name' => 'Premium Polo Shirt Corporate Line',
            'code' => 'TP-002-2025',
            'garment_type' => 'Polo',
            'version' => 2,
            'status' => 'approved',
            'description' => 'Corporate polo shirt with embroidered logo',
            'uploaded_at' => now()->subDays(10),
        ]);

        $techpack2 = Techpack::create([
            'client_id' => $clients[0]->id,
            'name' => 'Classic T-Shirt Basic Line',
            'code' => 'TP-001-2025',
            'garment_type' => 'Remera Básica',
            'version' => 1,
            'status' => 'approved',
            'description' => 'Basic cotton t-shirt',
            'uploaded_at' => now()->subDays(8),
        ]);

        $techpack3 = Techpack::create([
            'client_id' => $clients[1]->id,
            'name' => 'Sport Hoodie Performance Series',
            'code' => 'TP-003-2025',
            'garment_type' => 'Hoodie',
            'version' => 1,
            'status' => 'pending',
            'description' => 'Athletic hoodie with moisture-wicking fabric',
            'uploaded_at' => now()->subDays(3),
        ]);

        // Associate techpacks with quotes using many-to-many
        $quote1->techpacks()->attach([$techpack1->id, $techpack2->id]);
        $quote2->techpacks()->attach([$techpack3->id]);

        // Create sample orders
        SampleOrder::create([
            'techpack_id' => $techpack1->id,
            'supplier_id' => $suppliers[0]->id,
            'requested_by' => 'Juan Pérez',
            'requested_at' => now()->subDays(15),
            'eta' => now()->addDays(5),
            'sizes' => [
                'S' => ['client' => 1, 'wts' => 1, 'received' => 0],
                'M' => ['client' => 2, 'wts' => 1, 'received' => 1],
                'L' => ['client' => 2, 'wts' => 1, 'received' => 0],
                'XL' => ['client' => 1, 'wts' => 1, 'received' => 1],
            ],
            'status' => 'shipped',
            'shipping_address' => 'Av. Principal 123, Buenos Aires, Argentina',
            'courier' => 'DHL Express',
            'tracking_number' => 'DHL123456789',
            'shipped_at' => now()->subDays(2),
            'packages' => 1,
            'weight' => 2.5,
            'notes' => 'Handle with care',
            'attachments_count' => 2,
        ]);

        SampleOrder::create([
            'techpack_id' => $techpack2->id,
            'supplier_id' => $suppliers[0]->id,
            'requested_by' => 'María González',
            'requested_at' => now()->subDays(10),
            'eta' => now()->addDays(10),
            'sizes' => [
                'S' => ['client' => 1, 'wts' => 1, 'received' => 1],
                'M' => ['client' => 2, 'wts' => 1, 'received' => 2],
                'L' => ['client' => 2, 'wts' => 1, 'received' => 1],
            ],
            'status' => 'received',
            'shipping_address' => 'Calle Secundaria 456, Montevideo, Uruguay',
            'attachments_count' => 1,
        ]);

        SampleOrder::create([
            'techpack_id' => $techpack3->id,
            'supplier_id' => $suppliers[1]->id,
            'requested_by' => 'Robert Smith',
            'requested_at' => now()->subDays(7),
            'eta' => now()->addDays(12),
            'sizes' => [
                'S' => ['client' => 2, 'wts' => 1, 'received' => 0],
                'M' => ['client' => 3, 'wts' => 2, 'received' => 1],
                'L' => ['client' => 2, 'wts' => 1, 'received' => 0],
                'XL' => ['client' => 1, 'wts' => 1, 'received' => 0],
            ],
            'status' => 'in_production',
            'shipping_address' => 'Rua Industrial 590, São Paulo, Brasil',
            'notes' => 'Urgente para showroom del cliente',
            'attachments_count' => 0,
        ]);

        // Create production milestones for quote 1
        $milestones = [
            ['milestone' => 'Hilado', 'planned_at' => now()->subDays(5), 'actual_at' => now()->subDays(3), 'delay_days' => 2, 'status' => 'completed', 'comment' => 'Completado con 2 días de adelanto'],
            ['milestone' => 'Tejido', 'planned_at' => now()->addDays(5), 'actual_at' => now()->addDays(7), 'delay_days' => 2, 'status' => 'delayed', 'comment' => 'Retraso por falta de materia prima'],
            ['milestone' => 'Corte', 'planned_at' => now()->addDays(15), 'actual_at' => null, 'delay_days' => null, 'status' => 'pending', 'comment' => null],
            ['milestone' => 'Costura', 'planned_at' => now()->addDays(22), 'actual_at' => null, 'delay_days' => null, 'status' => 'pending', 'comment' => 'Equipo en stand-by'],
            ['milestone' => 'Lavado', 'planned_at' => now()->addDays(30), 'actual_at' => null, 'delay_days' => null, 'status' => 'pending', 'comment' => null],
            ['milestone' => 'Acabado', 'planned_at' => now()->addDays(38), 'actual_at' => null, 'delay_days' => null, 'status' => 'pending', 'comment' => null],
            ['milestone' => 'Empaque', 'planned_at' => now()->addDays(45), 'actual_at' => null, 'delay_days' => null, 'status' => 'pending', 'comment' => 'Esperando confirmación de cliente'],
            ['milestone' => 'Envío', 'planned_at' => now()->addDays(50), 'actual_at' => null, 'delay_days' => null, 'status' => 'pending', 'comment' => null],
        ];

        foreach ($milestones as $milestone) {
            ProductionMilestone::create([
                'quote_id' => $quote1->id,
                'milestone' => $milestone['milestone'],
                'planned_at' => $milestone['planned_at'],
                'actual_at' => $milestone['actual_at'],
                'delay_days' => $milestone['delay_days'],
                'status' => $milestone['status'],
                'comment' => $milestone['comment'],
                'updated_by' => 'WTS System',
            ]);
        }

        // Create purchase orders for quote 1
        PurchaseOrder::create([
            'quote_id' => $quote1->id,
            'file_path' => 'purchase-orders/po_2025-01-16_v1.pdf',
            'file_name' => 'PO-834893-v1.pdf',
            'version' => 1,
            'uploaded_by' => 'Admin',
            'notes' => 'Initial purchase order',
            'is_current' => false,
            'created_at' => now()->subDays(10),
        ]);

        PurchaseOrder::create([
            'quote_id' => $quote1->id,
            'file_path' => 'purchase-orders/po_2025-01-20_v2.pdf',
            'file_name' => 'PO-834893-v2.pdf',
            'version' => 2,
            'uploaded_by' => 'Admin',
            'notes' => 'Updated quantities and delivery date',
            'is_current' => true,
            'created_at' => now()->subDays(6),
        ]);

        // Create production milestones & PO for quote 2
        $milestonesQuote2 = [
            ['milestone' => 'Hilado', 'planned_at' => now()->subDays(2), 'actual_at' => now()->subDay(), 'delay_days' => 1, 'status' => 'completed', 'comment' => 'Finalizado con 1 día de retraso'],
            ['milestone' => 'Tejido', 'planned_at' => now()->addDays(6), 'actual_at' => null, 'delay_days' => null, 'status' => 'in_progress', 'comment' => 'Proveedor confirma materiales listos'],
            ['milestone' => 'Corte', 'planned_at' => now()->addDays(16), 'actual_at' => null, 'delay_days' => null, 'status' => 'pending', 'comment' => null],
            ['milestone' => 'Costura', 'planned_at' => now()->addDays(28), 'actual_at' => null, 'delay_days' => null, 'status' => 'pending', 'comment' => 'Revisar capacidad del equipo'],
            ['milestone' => 'Acabado', 'planned_at' => now()->addDays(38), 'actual_at' => null, 'delay_days' => null, 'status' => 'pending', 'comment' => null],
        ];

        foreach ($milestonesQuote2 as $milestone) {
            ProductionMilestone::create([
                'quote_id' => $quote2->id,
                'milestone' => $milestone['milestone'],
                'planned_at' => $milestone['planned_at'],
                'actual_at' => $milestone['actual_at'],
                'delay_days' => $milestone['delay_days'],
                'status' => $milestone['status'],
                'comment' => $milestone['comment'],
                'updated_by' => 'Equipo proveedor',
            ]);
        }

        PurchaseOrder::create([
            'quote_id' => $quote2->id,
            'file_path' => 'purchase-orders/po_2025-01-22_v1.pdf',
            'file_name' => 'PO-000123-v1.pdf',
            'version' => 1,
            'uploaded_by' => 'María González',
            'notes' => 'Condiciones de pago 30/70',
            'is_current' => true,
            'created_at' => now()->subDays(4),
        ]);

    }
}
