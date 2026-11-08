<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\BlogCategoryController as AdminBlogCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\PreOrderController as AdminPreOrderController;
use App\Http\Controllers\Admin\InstagramController as AdminInstagramController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\KaryawanDashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Admin\StockAlertController;
use App\Http\Controllers\Admin\StockHistoryController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\DiscountProductController;
use App\Http\Controllers\Admin\PromoCodeController;


Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth routes
Route::get('/login', [LoginController::class, 'index'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [LoginController::class, 'store'])
    ->middleware('guest');

Route::get('/register', [RegisterController::class, 'index'])
    ->name('register')
    ->middleware('guest');
Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');


Route::get('/shop', [ProductController::class, 'index'])->name('shop');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');



Route::get('/cart', [CartController::class, 'index'])->name('cart');

// subscribe dari frontend
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->name('newsletter.subscribe');

// unsubscribe dari email
Route::get('/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])
    ->name('newsletter.unsubscribe');


Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/discount/validate', [CheckoutController::class, 'validateDiscount'])->name('discount.validate');
    Route::get('/payment-callback', [CheckoutController::class, 'callback'])->name('payment.callback');

    Route::get('/dashboard', [OrderController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/orders/{order}', [OrderController::class, 'show'])->name('dashboard.order');
    Route::get('/dashboard/wishlist', function () {return view('dashboard.wishlist');})->name('dashboard.wishlist');
    Route::get('/dashboard/invoice/{order}', [OrderController::class, 'invoice'])->name('dashboard.invoice');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::patch('/read-all', [NotificationController::class, 'readAll'])->name('read-all');
        Route::patch('/{notification}/read', [NotificationController::class, 'read'])->name('read');
    });

    Route::put('/dashboard/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
    
    // 🔥 RETRY PAYMENT - Baru
    Route::post('/dashboard/orders/{order}/retry-payment', [OrderController::class, 'retryPayment'])->name('order.retry-payment');
    Route::post('/dashboard/orders/{order}/change-payment-method', [OrderController::class, 'changePaymentMethod'])->name('order.change-payment-method');
    
    // 🔥 Complete pre-order payment
    Route::get('/dashboard/orders/{order}/complete-payment/{item}', [OrderController::class, 'completePayment'])->name('order.complete-payment');
    Route::get('/dashboard/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/dashboard/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/dashboard/password', [ProfileController::class, 'updatePassword'])->name('dashboard.password.update');

    Route::post('/blog/comment', [BlogController::class, 'storeComment'])->name('blog.comment.store');

    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        return redirect('/');
    })->name('logout');
});

// Blog routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/blog/feed', [BlogController::class, 'feed'])->name('blog.feed');

// Community page
Route::get('/community', function () {
    return view('community');
})->name('community');

// About & Contact (placeholder)
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');

// Admin routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', AdminProductController::class);
    Route::post('products/{product}/toggle-active', [AdminProductController::class, 'toggleActive'])->name('products.toggle-active');

    // Stock Alerts & History
    Route::get('stock-alerts', [StockAlertController::class, 'index'])->name('stock-alerts.index');
    Route::get('stock-alerts/{stockAlert}', [StockAlertController::class, 'show'])->name('stock-alerts.show');
    Route::post('stock-alerts/{stockAlert}/acknowledge', [StockAlertController::class, 'acknowledge'])->name('stock-alerts.acknowledge');
    Route::post('stock-alerts/{stockAlert}/resolve', [StockAlertController::class, 'resolve'])->name('stock-alerts.resolve');
    Route::post('stock-alerts/bulk/acknowledge', [StockAlertController::class, 'bulkAcknowledge'])->name('stock-alerts.bulk-acknowledge');
    Route::post('stock-alerts/bulk/resolve', [StockAlertController::class, 'bulkResolve'])->name('stock-alerts.bulk-resolve');

    Route::get('stock-history', [StockHistoryController::class, 'index'])->name('stock-history.index');
    Route::get('stock-history/product/{product}', [StockHistoryController::class, 'product'])->name('stock-history.product');
    Route::get('stock-history/export', [StockHistoryController::class, 'export'])->name('stock-history.export');

    // Orders
    Route::resource('orders', AdminOrderController::class);
    Route::post('orders/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    // Blog
    Route::resource('blog/posts', AdminBlogController::class)
    ->names([
        'index' => 'blog.posts',
        'create' => 'blog.posts.create',
        'store' => 'blog.posts.store',
        'show' => 'blog.posts.show',
        'edit' => 'blog.posts.edit',
        'update' => 'blog.posts.update',
        'destroy' => 'blog.posts.destroy']);
    Route::delete('blog/comments/{comment}', [AdminBlogController::class, 'deleteComment'])->name('blog.comments.delete');
    Route::resource('blog/categories', AdminBlogCategoryController::class)
        ->names([
            'index' => 'blog.categories.index',
            'create' => 'blog.categories.create',
            'store' => 'blog.categories.store',
            'show' => 'blog.categories.show',
            'edit' => 'blog.categories.edit',
            'update' => 'blog.categories.update',
            'destroy' => 'blog.categories.destroy',
        ]);
    Route::get('blog/comments', [AdminBlogController::class, 'comments'])->name('blog.comments');
    Route::patch('blog/comments/{comment}/approve', [AdminBlogController::class, 'approveComment'])->name('blog.comments.approve');
    Route::delete('blog/comments/{comment}', [AdminBlogController::class, 'deleteComment'])->name('blog.comments.delete');

    // Pre-order
    Route::get('preorders', [AdminPreOrderController::class, 'index'])->name('preorders.index');
    Route::post('preorders/{orderItem}/ready', [AdminPreOrderController::class, 'markReady'])->name('preorders.ready');
    Route::get('preorders/report', [AdminPreOrderController::class, 'report'])->name('preorders.report');
    Route::post('preorders/bulk-ready', [AdminPreOrderController::class, 'bulkReady'])
    ->name('preorders.bulk-ready');

    // Discounts & Promo Codes
    Route::resource('discounts', DiscountController::class);
    Route::post('discounts/{discount}/toggle-status', [DiscountController::class, 'toggleStatus'])->name('discounts.toggle-status');
    Route::resource('discount-products', DiscountProductController::class)->except(['show']);
    Route::resource('promo-codes', PromoCodeController::class);
    Route::post('promo-codes/{promoCode}/toggle-status', [PromoCodeController::class, 'toggleStatus'])->name('promo-codes.toggle-status');

    // Instagram settings
    Route::get('instagram', [AdminInstagramController::class, 'index'])->name('instagram.index');
    Route::post('instagram/save', [AdminInstagramController::class, 'save'])->name('instagram.save');
    Route::post('instagram/refresh', [AdminInstagramController::class, 'refresh'])->name('instagram.refresh');
    Route::get('instagram/test', [AdminInstagramController::class, 'test'])
    ->name('instagram.test');

    // Settings
    Route::get('settings/general', [AdminSettingsController::class, 'editGeneral'])->name('settings.general');
    Route::post('settings/general', [AdminSettingsController::class, 'updateGeneral'])->name('settings.general.update');
    Route::get('settings/payment', [AdminSettingsController::class, 'editPayment'])->name('settings.payment');
    Route::post('settings/payment', [AdminSettingsController::class, 'updatePayment'])->name('settings.payment.update');
    Route::get('settings/shipping', [AdminSettingsController::class, 'editShipping'])->name('settings.shipping');
    Route::post('settings/shipping', [AdminSettingsController::class, 'updateShipping'])->name('settings.shipping.update');

    // Media & Home Page Images
    Route::get('media', [AdminMediaController::class, 'index'])->name('media.index');
    Route::post('media/hero-image', [AdminMediaController::class, 'uploadHeroImage'])->name('media.upload.hero');
    Route::post('media/story-image', [AdminMediaController::class, 'uploadStoryImage'])->name('media.upload.story');
    Route::post('media/editorial-image', [AdminMediaController::class, 'uploadEditorialImage'])->name('media.upload.editorial');
    Route::post('media/reset-image', [AdminMediaController::class, 'resetImage'])->name('media.reset');
    Route::post('media/settings', [AdminMediaController::class, 'updateSettings'])->name('media.settings');

    // Product image delete
    Route::delete('products/image/{image}', [AdminProductController::class, 'deleteImage'])->name('products.image.destroy');


    //  list subscriber
    Route::resource
    ('newsletter/subscribers', NewsletterController::class)
    ->names([
        'index' => 'newsletter.subscribers.index',
        'create' => 'newsletter.subscribers.create',
        'store' => 'newsletter.subscribers.store',
        'destroy' => 'newsletter.subscribers.destroy',

    ]);
    Route::get('newsletter/subscribers/export',
            [NewsletterController::class, 'export']
        )->name('newsletter.subscribers.export');

        Route::get('newsletter/campaign',
            [NewsletterController::class, 'campaign']
        )->name('newsletter.campaign');

        Route::post('newsletter/send',
            [NewsletterController::class, 'send']
        )->name('newsletter.send');

        Route::get('newsletter/history',
            [NewsletterController::class, 'history']
        )->name('newsletter.history');
});

// Karyawan (Employee/Warehouse) routes
Route::prefix('karyawan')->middleware(['auth', 'role:karyawan'])->name('karyawan.')->group(function () {
    Route::get('/', \App\Http\Controllers\Karyawan\DashboardController::class . '@dashboard')->name('dashboard');

    // Stock management
    Route::resource('stock', \App\Http\Controllers\Karyawan\StockController::class);

    // View orders for fulfillment
    Route::get('orders', \App\Http\Controllers\Karyawan\OrderController::class . '@index')->name('orders.index');
    Route::get('orders/{order}', \App\Http\Controllers\Karyawan\OrderController::class . '@show')->name('orders.show');
    Route::patch('orders/{order}/status', \App\Http\Controllers\Karyawan\OrderController::class . '@updateStatus')->name('orders.updateStatus');

    // View products
    Route::get('products', \App\Http\Controllers\Karyawan\ProductController::class . '@index')->name('products.index');
});

// Kasir (Cashier) routes
Route::prefix('kasir')->middleware(['auth', 'role:kasir'])->name('kasir.')->group(function () {
    Route::get('/', \App\Http\Controllers\Kasir\OrderController::class . '@dashboard')->name('dashboard');

    // Process payment
    Route::get('orders', \App\Http\Controllers\Kasir\OrderController::class . '@index')->name('orders.index');
    Route::get('orders/{order}', \App\Http\Controllers\Kasir\OrderController::class . '@show')->name('orders.show');
    Route::post('orders/{order}/payment', \App\Http\Controllers\Kasir\OrderController::class . '@processPayment')->name('orders.payment');

    // Reports
    Route::get('reports/daily', \App\Http\Controllers\Kasir\OrderController::class . '@dailyReport')->name('reports.daily');
    Route::get('reports/weekly', \App\Http\Controllers\Kasir\OrderController::class . '@weeklyReport')->name('reports.weekly');
    Route::get('reports/cashflow', \App\Http\Controllers\Kasir\OrderController::class . '@cashflow')->name('reports.cashflow');

    Route::get('transaksi', function () {return view('kasir.transaksi.index');})->name('transaksi');
    Route::get('history', \App\Http\Controllers\Kasir\OrderController::class . '@history')->name('history');

});
Route::post('/payment-callback', [App\Http\Controllers\CheckoutController::class, 'callback'])
    ->name('payment.callback');
// routes/web.php
Route::get('/test-rajaongkir', function () {
    $service = new App\Services\RajaOngkirService();
    return response()->json($service->testConnection());
})->middleware('auth');
Route::get('/test/midtrans', function () {
    $midtrans = new App\Services\MidtransService();

    try {
        $midtrans->createTransaction(
            (object) ['order_number' => 'TEST-123', 'total_amount' => 10000],
            [['id' => 'test', 'price' => 10000, 'quantity' => 1, 'name' => 'Test Product']],
            ['first_name' => 'Test', 'email' => 'test@example.com']
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Midtrans configured correctly'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
});
// routes/web.php

// 🔥 Route untuk test callback manual (hanya untuk development)
Route::post('/test-callback', function (Illuminate\Http\Request $request) {
    Log::info('Test Callback Received:', $request->all());
    
    // Simulasikan pembayaran berhasil
    $orderNumber = $request->order_id ?? 'ORD-20260809-ABC123';
    $order = App\Models\Order::where('order_number', $orderNumber)->first();
    
    if ($order) {
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing'
        ]);
        Log::info('Test callback: Order updated', ['order_id' => $order->id]);
        return response()->json(['message' => 'Order updated successfully']);
    }
    
    return response()->json(['message' => 'Order not found'], 404);
})->name('test.callback');
