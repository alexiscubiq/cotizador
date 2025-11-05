<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Supplier;
use App\Models\QuoteType;
use App\Models\Techpack;
use App\Models\Quote;
use App\Models\SampleOrder;
use App\Models\ProductionMilestone;
use App\Models\Tna;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create Users
        $wtsUser = User::create([
            'name' => 'Admin WTS',
            'email' => 'admin@wts.com',
            'password' => Hash::make('password'),
            'user_type' => 'wts_internal',
        ]);

        $supplierUser = User::create([
            'name' => 'Proveedor Demo',
            'email' => 'supplier@demo.com',
            'password' => Hash::make('password'),
            'user_type' => 'supplier',
        ]);

        // Create Clients
        $clients = [
            ['name' => 'Nike Inc', 'email' => 'contact@nike.com', 'phone' => '+1-800-344-6453'],
            ['name' => 'Adidas AG', 'email' => 'info@adidas.com', 'phone' => '+49-9132-84-0'],
            ['name' => 'Zara (Inditex)', 'email' => 'contact@zara.com', 'phone' => '+34-981-185-400'],
            ['name' => 'H&M', 'email' => 'info@hm.com', 'phone' => '+46-8-796-55-00'],
            ['name' => 'GAP Inc', 'email' => 'contact@gap.com', 'phone' => '+1-800-427-7895'],
        ];

        foreach ($clients as $clientData) {
            Client::create($clientData);
        }

        // Create Suppliers
        $suppliers = [
            ['name' => 'Tex Manufacturing Ltd', 'email' => 'sales@texmfg.com', 'phone' => '+880-2-8832456', 'country' => 'Bangladesh'],
            ['name' => 'Asian Apparel Co', 'email' => 'info@asianapparel.com', 'phone' => '+84-28-3825-6789', 'country' => 'Vietnam'],
            ['name' => 'China Garments Factory', 'email' => 'export@chinagar.com', 'phone' => '+86-20-8888-9999', 'country' => 'China'],
            ['name' => 'India Textiles Pvt', 'email' => 'sales@indiatex.com', 'phone' => '+91-11-4567-8900', 'country' => 'India'],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }

        // Create Quote Types
        $quoteTypes = [
            ['name' => 'FOB', 'description' => 'Free On Board', 'is_active' => true],
            ['name' => 'CIF', 'description' => 'Cost, Insurance & Freight', 'is_active' => true],
            ['name' => 'EXW', 'description' => 'Ex Works', 'is_active' => true],
            ['name' => 'DDU', 'description' => 'Delivered Duty Unpaid', 'is_active' => true],
        ];

        foreach ($quoteTypes as $typeData) {
            QuoteType::create($typeData);
        }

        // Create Tech Packs
        $buyers = ['Nike', 'Adidas', 'Zara', 'H&M', 'GAP'];
        $departments = ['Men\'s', 'Women\'s', 'Kids', 'Unisex'];
        $seasons = ['SS25', 'FW25', 'SS26'];
        $garmentTypes = ['T-Shirt', 'Polo', 'Hoodie', 'Jogger', 'Jean'];

        $techpacks = [];
        for ($i = 1; $i <= 15; $i++) {
            $client = Client::inRandomOrder()->first();
            $buyer = $buyers[array_rand($buyers)];
            $dept = $departments[array_rand($departments)];
            $season = $seasons[array_rand($seasons)];
            $garment = $garmentTypes[array_rand($garmentTypes)];
            $status = ['draft', 'pending', 'approved', 'approved', 'approved'][array_rand(['draft', 'pending', 'approved', 'approved', 'approved'])];

            $techpack = Techpack::create([
                'client_id' => $client->id,
                'code' => 'TP-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'style_code' => $status === 'approved' ? 'STYLE-' . str_pad($i, 6, '0', STR_PAD_LEFT) : null,
                'wfx_id' => $status === 'approved' && rand(0, 1) ? 'WFX-' . strtoupper(substr(md5($i), 0, 8)) : null,
                'name' => "{$garment} {$dept} {$season}",
                'buyer' => $buyer,
                'buyer_department' => $dept,
                'season' => $season,
                'garment_type' => $garment,
                'version' => rand(1, 3),
                'status' => $status,
                'description' => "Tech Pack para {$garment} de la temporada {$season}, departamento {$dept}.",
                'synced_to_wfx_at' => $status === 'approved' && rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
            ]);

            if ($status === 'approved') {
                $techpacks[] = $techpack;
            }
        }

        // Create Quotes with detailed information
        foreach ($techpacks as $index => $techpack) {
            $supplier = Supplier::inRandomOrder()->first();
            $quoteType = QuoteType::inRandomOrder()->first();
            $status = ['draft', 'pending', 'in_production', 'completed'][array_rand(['draft', 'pending', 'in_production', 'completed'])];

            $fabricInfo = [
                [
                    'fabric_name' => '100% Cotton Jersey',
                    'composition' => '100% Cotton',
                    'weight' => '180 GSM',
                    'construction' => 'Jersey',
                    'yarn_count' => '30/1',
                    'dyeing_type' => 'Piece Dye',
                    'special_finishes' => 'Enzyme Wash, Soft Hand Feel',
                ],
                [
                    'fabric_name' => 'Cotton Spandex Rib',
                    'composition' => '95% Cotton, 5% Spandex',
                    'weight' => '220 GSM',
                    'construction' => 'Rib',
                    'yarn_count' => '30/1 + 20 den',
                    'dyeing_type' => 'Yarn Dye',
                    'special_finishes' => 'Peach Finish',
                ],
            ];

            $trimsList = [
                [
                    'trim_name' => 'Main Label',
                    'trim_code' => 'LBL-001',
                    'trim_specs' => 'Woven label, 50mm x 25mm, 4-color printing',
                ],
                [
                    'trim_name' => 'Care Label',
                    'trim_code' => 'LBL-002',
                    'trim_specs' => 'Printed satin label, 40mm x 60mm',
                ],
                [
                    'trim_name' => 'Hangtag',
                    'trim_code' => 'TAG-001',
                    'trim_specs' => '300gsm cardboard, full color, matte finish',
                ],
            ];

            $artworkDetails = [
                [
                    'artwork_name' => 'Logo Principal',
                    'artwork_type' => 'Screen Print',
                    'artwork_location' => 'Frente Centro',
                    'artwork_notes' => 'Plastisol ink, 2 colores',
                ],
                [
                    'artwork_name' => 'Gráfico Espalda',
                    'artwork_type' => 'Screen Print',
                    'artwork_location' => 'Espalda',
                    'artwork_notes' => 'Plastisol ink, 4 colores, 12x16 inches',
                ],
            ];

            $costsheetData = [
                'materials' => [
                    ['item' => 'Fabric', 'cost' => 3.50],
                    ['item' => 'Trims', 'cost' => 0.80],
                    ['item' => 'Packaging', 'cost' => 0.30],
                ],
                'labor' => [
                    ['item' => 'Cutting', 'cost' => 0.40],
                    ['item' => 'Sewing', 'cost' => 1.20],
                    ['item' => 'Finishing', 'cost' => 0.30],
                ],
                'overhead' => [
                    ['item' => 'Factory Overhead', 'cost' => 0.80],
                    ['item' => 'Testing & QC', 'cost' => 0.20],
                ],
            ];

            Quote::create([
                'code' => 'RFQ-' . str_pad(($index + 1) * 100, 6, '0', STR_PAD_LEFT),
                'client_id' => $techpack->client_id,
                'supplier_id' => $supplier->id,
                'quote_type_id' => $quoteType->id,
                'buyer' => $techpack->buyer,
                'buyer_department' => $techpack->buyer_department,
                'season' => $techpack->season,
                'created_date' => now()->subDays(rand(5, 60)),
                'delivery_date' => now()->addDays(rand(5, 30)),
                'quantity' => rand(1000, 10000),
                'unit_price' => rand(8, 25) + (rand(0, 99) / 100),
                'estimated_cost' => rand(6, 18) + (rand(0, 99) / 100),
                'profit_margin' => rand(15, 40) + (rand(0, 9) / 10),
                'lead_time_days' => rand(45, 90),
                'minimums_by_style' => rand(500, 2000),
                'minimums_by_color' => [
                    'Black' => 300,
                    'White' => 300,
                    'Navy' => 200,
                    'Gray' => 200,
                ],
                'minimums_by_fabric' => [
                    'Jersey Cotton' => '1 roll = 1000 yards = 1500 units',
                    'Rib 2x2' => '1 roll = 800 yards = 400 units (neckline)',
                ],
                'size_range' => ['XS-XL', 'S-2XL', 'M-3XL'][array_rand(['XS-XL', 'S-2XL', 'M-3XL'])],
                'fabric_information' => $fabricInfo,
                'trims_list' => $trimsList,
                'artwork_details' => $artworkDetails,
                'costsheet_data' => $costsheetData,
                'status' => $status,
                'has_artwork' => true,
            ])->techpacks()->attach($techpack->id);
        }

        // Create TNAs
        $this->command->info('Creating TNAs...');
        $quotes = Quote::take(3)->get();

        foreach ($quotes as $quote) {
            $startDate = Carbon::now()->subDays(10);
            $milestones = [
                [
                    'task' => 'Fabric Sourcing & Approval',
                    'responsible' => 'Sourcing Team',
                    'due_date' => $startDate->copy()->addDays(5)->format('Y-m-d'),
                    'status' => 'completed',
                    'completed_date' => $startDate->copy()->addDays(4)->format('Y-m-d'),
                    'notes' => 'All fabrics approved by client'
                ],
                [
                    'task' => 'Lab Dip Submission',
                    'responsible' => 'Lab Team',
                    'due_date' => $startDate->copy()->addDays(8)->format('Y-m-d'),
                    'status' => 'completed',
                    'completed_date' => $startDate->copy()->addDays(7)->format('Y-m-d'),
                    'notes' => 'Submitted 3 options, option 2 selected'
                ],
                [
                    'task' => 'Strike-off Approval',
                    'responsible' => 'Print Team',
                    'due_date' => $startDate->copy()->addDays(12)->format('Y-m-d'),
                    'status' => 'in_progress',
                    'notes' => 'Waiting for client feedback'
                ],
                [
                    'task' => 'Pre-production Sample',
                    'responsible' => 'Sample Room',
                    'due_date' => $startDate->copy()->addDays(15)->format('Y-m-d'),
                    'status' => 'pending',
                    'notes' => 'Will start after strike-off approval'
                ],
                [
                    'task' => 'Trim Cards Submission',
                    'responsible' => 'Trim Team',
                    'due_date' => $startDate->copy()->addDays(17)->format('Y-m-d'),
                    'status' => 'pending',
                    'notes' => 'Buttons and zipper samples ready'
                ],
                [
                    'task' => 'Size Set Sample',
                    'responsible' => 'Sample Room',
                    'due_date' => $startDate->copy()->addDays(20)->format('Y-m-d'),
                    'status' => 'pending',
                    'notes' => 'All sizes to be made'
                ],
                [
                    'task' => 'Final PP Meeting',
                    'responsible' => 'Production Manager',
                    'due_date' => $startDate->copy()->addDays(23)->format('Y-m-d'),
                    'status' => 'pending',
                    'notes' => 'Review all samples and specs'
                ],
                [
                    'task' => 'Bulk Fabric In-house',
                    'responsible' => 'Warehouse',
                    'due_date' => $startDate->copy()->addDays(25)->format('Y-m-d'),
                    'status' => 'pending',
                    'notes' => 'Fabric ordered from mill'
                ],
                [
                    'task' => 'Cutting Start',
                    'responsible' => 'Cutting Department',
                    'due_date' => $startDate->copy()->addDays(27)->format('Y-m-d'),
                    'status' => 'pending',
                    'notes' => 'CAD files ready'
                ],
                [
                    'task' => 'Sewing Start',
                    'responsible' => 'Sewing Floor',
                    'due_date' => $startDate->copy()->addDays(30)->format('Y-m-d'),
                    'status' => 'pending',
                    'notes' => 'Line allocation confirmed'
                ],
                [
                    'task' => 'Inline Inspection',
                    'responsible' => 'QA Team',
                    'due_date' => $startDate->copy()->addDays(35)->format('Y-m-d'),
                    'status' => 'pending',
                    'notes' => 'Check 300 pieces at 50% production'
                ],
                [
                    'task' => 'Final Inspection',
                    'responsible' => 'QA Team',
                    'due_date' => $startDate->copy()->addDays(40)->format('Y-m-d'),
                    'status' => 'pending',
                    'notes' => 'AQL 2.5 standard'
                ],
                [
                    'task' => 'Packing & Carton Marking',
                    'responsible' => 'Packing Team',
                    'due_date' => $startDate->copy()->addDays(42)->format('Y-m-d'),
                    'status' => 'pending',
                    'notes' => 'According to packing list'
                ],
                [
                    'task' => 'Shipment',
                    'responsible' => 'Logistics',
                    'due_date' => $startDate->copy()->addDays(45)->format('Y-m-d'),
                    'status' => 'pending',
                    'notes' => 'FOB port ready'
                ],
            ];

            $tna = Tna::create([
                'quote_id' => $quote->id,
                'name' => 'TNA - ' . $quote->code,
                'description' => 'Time and Action plan for ' . $quote->code,
                'start_date' => $startDate,
                'end_date' => $startDate->copy()->addDays(45),
                'milestones' => $milestones,
                'status' => 'active',
                'imported_from' => 'Seeder',
                'imported_at' => now(),
                'metadata' => [
                    'total_milestones' => count($milestones),
                    'created_via' => 'demo_seeder',
                ]
            ]);

            // Attach 1-2 techpacks to each TNA
            $techpacksToAttach = $quote->techpacks->take(rand(1, 2))->pluck('id');
            $tna->techpacks()->attach($techpacksToAttach);

            // Update status based on milestones
            $tna->updateStatus();
        }

        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->info('👤 WTS User: admin@wts.com / password');
        $this->command->info('👤 Supplier: supplier@demo.com / password');
    }
}
