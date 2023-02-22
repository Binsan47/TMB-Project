<?php

namespace App\Http\Livewire;

use Cart;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;


class ShopComponent extends Component
{
    public $sorting;
    public $pagesize;

    public $min_price;
    public $max_price;

    public $category;

    public function mount()
    {
        $this->sorting = "default";
        $this->pagesize = 12;

        $this->min_price = 1000;
        $this->max_price = 100000;
    }

    public function store($products_id,$products_name,$products_price)
    {
        Cart::instance('cart')->add($products_id,$products_name,1,$products_price)->associate('App\Models\Product');
        session()->flash('success_message','Item added in Cart');
        return redirect()->route('products.cart');
    }

    public function addToWishlist($products_id,$products_name,$products_price)
    {
        Cart::instance('wishlist')->add($products_id,$products_name,1,$products_price)->associate('App\Models\Product');
        $this->emitTo('wishlist-count-component','refreshComponent');
    }

    public function removeFromWishlist($products_id)
    {
        foreach(Cart::instance('wishlist')->content() as $witem)
        {
            if($witem->id == $products_id)
            {
                Cart::instance('wishlist')->remove($witem->rowId);
                $this->emitTo('wishlist-count-component','refreshComponent');
                return;
            }
        }
    }
    
    use WithPagination;
    public function render()
    {
        if($this->sorting=='date')
        {
            $products = Product::whereBetween('regular_price',[$this->min_price,$this->max_price])->orderBy('created_at','DESC')->paginate($this->pagesize);
        }
        else if($this->sorting=='price')
        {
            $products = Product::whereBetween('regular_price',[$this->min_price,$this->max_price])->orderBy('regular_price','ASC')->paginate($this->pagesize);
        }
        else if($this->sorting=='price-desc')
        {
            $products = Product::whereBetween('regular_price',[$this->min_price,$this->max_price])->orderBy('regular_price','DESC')->paginate($this->pagesize);
        }
        else{
            $products = Product::whereBetween('regular_price',[$this->min_price,$this->max_price])->paginate($this->pagesize);
        }

        $categories = Category::all();
        return view('livewire.shop-component',['products'=> $products,'categories'=>$categories])->layout('layouts.base');
    }
}
