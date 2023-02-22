<?php

namespace App\Http\Livewire;

use App\Models\Product;
use App\Models\Sale;
use Livewire\Component;
use Cart;

class DetailsComponent extends Component
{
    public $slug;
    public $qty;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->qty = 1;
    }

    public function store($products_id,$products_name,$products_price)
    {
        Cart::instance('cart')->add($products_id,$products_name,$this->qty,$products_price)->associate('App\Models\Product');
        session()->flash('success_message','Item added in Cart');
        return redirect()->route('products.cart');
    }

    public function inceaseQuantity()
    {
        $this->qty++;
    }

    public function decreaseQuantity()
    {
        if($this->qty > 1)
        {
            $this->qty--;
        }
    }

    public function render()
    {
        $product = Product::where('slug', $this->slug)->first();
        $popular_product = Product::inRandomOrder()->limit(4)->get();
        $related_product = Product::where('category_id', $product->category_id)->inRandomOrder()->limit(5)->get();
        $sale = Sale::find(1);
        return view('livewire.detail-component',['product' => $product,'popular_products'=>$popular_product,'related_products'=>$related_product,'sale'=>$sale])->layout('layouts.base');
    }
}
