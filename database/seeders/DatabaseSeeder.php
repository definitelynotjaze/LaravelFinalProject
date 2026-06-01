<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin user
        User::firstOrCreate(['email' => 'admin@brewcraft.ph'], [
            'first_name'   => 'Admin',
            'last_name'    => 'BrewCraft',
            'email'        => 'admin@brewcraft.ph',
            'password'     => Hash::make('password'),
            'role'         => 'admin',
            'phone'        => '+63 917 000 0001',
            'preferences'  => ['email_orders'=>true,'email_promos'=>true,'sms_notifications'=>false],
        ]);

        // ── Staff user
        User::firstOrCreate(['email' => 'staff@brewcraft.ph'], [
            'first_name'   => 'Maria',
            'last_name'    => 'Santos',
            'email'        => 'staff@brewcraft.ph',
            'password'     => Hash::make('password'),
            'role'         => 'staff',
            'phone'        => '+63 917 000 0002',
        ]);

        // ── Sample customer
        User::firstOrCreate(['email' => 'user@brewcraft.ph'], [
            'first_name'   => 'Juan',
            'last_name'    => 'dela Cruz',
            'email'        => 'user@brewcraft.ph',
            'password'     => Hash::make('password'),
            'role'         => 'user',
            'phone'        => '+63 917 000 0003',
        ]);

        // ── Menu items
        $items = [
            // Hot Coffee
            ['name'=>'Espresso','category'=>'Hot Coffee','price'=>95,'emoji'=>'☕','description'=>'Pure, bold single-shot espresso with a rich crema.','is_featured'=>true],
            ['name'=>'Americano','category'=>'Hot Coffee','price'=>110,'emoji'=>'☕','description'=>'Espresso diluted with hot water for a smooth, full-bodied cup.'],
            ['name'=>'Flat White','category'=>'Hot Coffee','price'=>145,'emoji'=>'☕','description'=>'Ristretto shots with velvety microfoam milk.','is_featured'=>true],
            ['name'=>'Cappuccino','category'=>'Hot Coffee','price'=>135,'emoji'=>'☕','description'=>'Equal parts espresso, steamed milk, and thick foam.'],
            ['name'=>'Latte','category'=>'Hot Coffee','price'=>145,'emoji'=>'☕','description'=>'Espresso with steamed milk and light foam.'],
            ['name'=>'Mocha','category'=>'Hot Coffee','price'=>155,'emoji'=>'☕','description'=>'Espresso with chocolate syrup and steamed milk.'],

            // Iced Coffee
            ['name'=>'Iced Americano','category'=>'Iced Coffee','price'=>120,'emoji'=>'🧊','description'=>'Espresso over ice with cold water. Clean and refreshing.','is_featured'=>true],
            ['name'=>'Iced Latte','category'=>'Iced Coffee','price'=>155,'emoji'=>'🧊','description'=>'Espresso, cold milk, and ice. The everyday crowd-pleaser.'],
            ['name'=>'Caramel Macchiato','category'=>'Iced Coffee','price'=>185,'emoji'=>'🧊','description'=>'Vanilla syrup, cold milk, espresso, and caramel drizzle.','is_featured'=>true],
            ['name'=>'Cold Brew','category'=>'Iced Coffee','price'=>165,'emoji'=>'🧊','description'=>'Steeped 18 hours for ultra-smooth, low-acid cold coffee.'],
            ['name'=>'Iced Mocha','category'=>'Iced Coffee','price'=>175,'emoji'=>'🧊','description'=>'Rich chocolate, espresso, and cold milk over ice.'],

            // Non-Coffee
            ['name'=>'Matcha Latte','category'=>'Non-Coffee','price'=>165,'emoji'=>'🍵','description'=>'Ceremonial-grade matcha with steamed or cold oat milk.','is_featured'=>true],
            ['name'=>'Strawberry Smoothie','category'=>'Non-Coffee','price'=>155,'emoji'=>'🍓','description'=>'Blended fresh strawberries with milk and honey.'],
            ['name'=>'Mango Shake','category'=>'Non-Coffee','price'=>155,'emoji'=>'🥭','description'=>'Creamy ripe mango blended to perfection.'],
            ['name'=>'Hot Chocolate','category'=>'Non-Coffee','price'=>145,'emoji'=>'🍫','description'=>'Rich Belgian chocolate with steamed whole milk.'],

            // Tea & Others
            ['name'=>'Thai Milk Tea','category'=>'Tea & Others','price'=>145,'emoji'=>'🧋','description'=>'Thai tea with condensed milk and tapioca pearls.'],
            ['name'=>'Wintermelon Tea','category'=>'Tea & Others','price'=>135,'emoji'=>'🧋','description'=>'Lightly sweet wintermelon sugar with milk tea.'],
            ['name'=>'Lemon Iced Tea','category'=>'Tea & Others','price'=>95,'emoji'=>'🍋','description'=>'Brewed black tea with fresh lemon juice and sugar.'],

            // Pastries
            ['name'=>'Butter Croissant','category'=>'Pastries','price'=>85,'emoji'=>'🥐','description'=>'Flaky, buttery croissant baked fresh every morning.','is_featured'=>true],
            ['name'=>'Blueberry Muffin','category'=>'Pastries','price'=>95,'emoji'=>'🫐','description'=>'Moist muffin loaded with fresh blueberries.'],
            ['name'=>'Cinnamon Roll','category'=>'Pastries','price'=>105,'emoji'=>'🌀','description'=>'Soft roll with cinnamon sugar swirl and cream cheese glaze.'],
            ['name'=>'Banana Bread','category'=>'Pastries','price'=>90,'emoji'=>'🍌','description'=>'Moist slice of house-made banana bread.'],

            // Snacks
            ['name'=>'Club Sandwich','category'=>'Snacks','price'=>185,'emoji'=>'🥪','description'=>'Triple-layer toasted sandwich with ham, egg, lettuce, and tomato.'],
            ['name'=>'Cheese Toast','category'=>'Snacks','price'=>115,'emoji'=>'🍞','description'=>'Thick-cut toast with melted cheddar and herbs.'],
        ];

        foreach ($items as $item) {
            MenuItem::firstOrCreate(
                ['name' => $item['name']],
                array_merge(['is_available'=>true,'is_featured'=>false,'prep_time'=>5], $item)
            );
        }
    }
}
