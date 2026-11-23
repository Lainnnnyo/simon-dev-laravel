<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('users',function(Blueprint $t){$t->boolean('is_staff')->default(false);$t->boolean('is_admin')->default(false);$t->softDeletes();});
  Schema::table('customers',function(Blueprint $t){$t->string('phone',30)->nullable()->change();$t->softDeletes();});
  Schema::create('products',function(Blueprint $t){
   $t->id();$t->string('name');$t->decimal('price',10,2);$t->unsignedInteger('stock_quantity')->default(0);
   $t->string('image')->nullable();$t->boolean('is_archived')->default(false);$t->timestamps();
  });
  Schema::create('sales',function(Blueprint $t){
   $t->id();$t->foreignId('product_id')->constrained()->restrictOnDelete();
   $t->foreignId('customer_id')->nullable()->constrained()->restrictOnDelete();
   $t->foreignId('sold_by')->constrained('users')->restrictOnDelete();
   $t->unsignedInteger('quantity');$t->decimal('unit_price',10,2);$t->decimal('total_price',14,2);
   $t->string('product_name');$t->string('customer_name')->nullable();$t->string('staff_name');
   $t->uuid('request_key')->unique();$t->timestamps();
  });
 }
 public function down(): void {
  Schema::dropIfExists('sales');Schema::dropIfExists('products');
  Schema::table('customers',fn(Blueprint $t)=>$t->dropSoftDeletes());
  Schema::table('users',function(Blueprint $t){$t->dropColumn(['is_staff','is_admin']);$t->dropSoftDeletes();});
 }
};
