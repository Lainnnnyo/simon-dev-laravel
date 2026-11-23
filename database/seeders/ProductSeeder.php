<?php
namespace Database\Seeders;
use App\Models\Product;
use Illuminate\Database\Seeder;
class ProductSeeder extends Seeder {
 public function run(): void {
  foreach([['Espresso','90.00',30],['Cyber Latte','140.00',30],['Cold Brew','125.00',25],['Mocha','155.00',20],['Butter Croissant','95.00',15]] as [$name,$price,$stock]){
   Product::firstOrCreate(['name'=>$name],['price'=>$price,'stock_quantity'=>$stock]);
  }
 }
}
