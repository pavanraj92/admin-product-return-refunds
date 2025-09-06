<?php

namespace admin\product_return_refunds\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Support\Facades\Config;
use admin\products\Models\Product;
use admin\products\Models\Order;
use admin\users\Models\User;

class ReturnRefundRequest extends Model
{
    use SoftDeletes, Sortable;

    protected $fillable = [
        'order_id',
        'product_id',
        'user_id',
        'seller_id',
        'request_type',
        'reason',
        'description',
        'status',
        'refund_amount',
        'refund_method',
        'refund_processed_at',
        'return_tracking_number',
        'return_shipping_carrier'
    ];

    protected $sortable = [
        'user',
        'product.name',
        'request_type',
        'status',
        'created_at',
    ];

    protected $casts = [
        'refund_processed_at' => 'datetime',
    ];

    public function userSortable($query, $direction)
    {
         return $query
        ->leftJoin('users', 'return_refund_requests.user_id', '=', 'users.id')
        ->orderByRaw("CONCAT(users.first_name, ' ', users.last_name) {$direction}")
        ->select('return_refund_requests.*');
    }

    public function productSortable($query, $direction)
    {
        return $query->join('products', 'return_refund_requests.product_id', '=', 'products.id')
            ->orderBy('products.name', $direction)
            ->select('return_refund_requests.*');
    }

    public function scopeFilter($query, $keyword)
    {
        if ($keyword) {
            return $query->whereHas('user', function ($q) use ($keyword) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) like ?", ['%' . $keyword . '%']);
            })
            ->orWhereHas('product', function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%');
            });
        }
        return $query;
    }
    /**
     * filter by status
     */
    public function scopeFilterByStatus($query, $status)
    {
        if (!is_null($status)) {
            return $query->where('status', $status);
        }

        return $query;
    }

    public function scopeFilterByRequestType($query, $request_type)
    {
        if (!is_null($request_type)) {
            return $query->where('request_type', $request_type);
        }

        return $query;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);

    }

    public static function getPerPageLimit(): int
    {
        return Config::has('get.admin_page_limit')
            ? Config::get('get.admin_page_limit')
            : 10;
    }
}