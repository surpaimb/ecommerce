<?php

function generateCrud($name, $controllerName) {

    Route::resource($name, $controllerName, ['names' => [
        'index'     => 'hideyo.'.$name.'.index',
        'create'    => 'hideyo.'.$name.'.create',
        'store'     => 'hideyo.'.$name.'.store',
        'edit'      => 'hideyo.'.$name.'.edit',
        'update'    => 'hideyo.'.$name.'.update',
        'destroy'   => 'hideyo.'.$name.'.destroy'
    ]]);
}

Route::group(['prefix' => config()->get('hideyo.route_prefix').'/admin', 'namespace' => 'Hideyo\Backend\Controllers', 'middleware' => ['web','auth.hideyo.backend']], function () {
 
    Route::get('/', array('as' => 'hideyo.index', 'uses' => 'DashboardController@index'));
   
    generateCrud('dashboard', 'DashboardController');

    Route::get('dashboard/stats/revenue-by-year/{year}', array('as' => 'admin.dashboard.stats', 'uses' => 'Hideyo\Shop\Controllers\Backend\DashboardController@getStatsRevenueByYear'));
    Route::get('dashboard/stats/order-average-by-year/{year}', array('as' => 'admin.dashboard.stats.average', 'uses' => 'Hideyo\Shop\Controllers\Backend\DashboardController@getStatsOrderAverageByYear'));
    Route::get('dashboard/stats/browser-by-year/{year}', array('as' => 'admin.dashboard.stats.browser', 'uses' => 'Hideyo\Shop\Controllers\Backend\DashboardController@getStatsBrowserByYear'));
    Route::get('dashboard/stats/totals-by-year/{year}', array('as' => 'admin.dashboard.stats.total', 'uses' => 'Hideyo\Shop\Controllers\Backend\DashboardController@getStatsTotalsByYear'));
    Route::get('dashboard/stats/payment-method-by-year/{year}', array('as' => 'admin.dashboard.stats.payment.method', 'uses' => 'Hideyo\Shop\Controllers\Backend\DashboardController@getStatsPaymentMethodByYear'));
    Route::get('dashboard/stats', array('as' => 'admin.dashboard.stats', 'uses' => 'Hideyo\Shop\Controllers\Backend\DashboardController@showStats'));
    
    generateCrud('shop', 'ShopController');

    Route::post('client/export', array('as' => 'hideyo.client.export', 'uses' => 'ClientController@postExport'));
    Route::get('client/export', array('as' => 'hideyo.client.export', 'uses' => 'ClientController@getExport'));

    Route::get('client/{clientId}/activate', array('as' => 'hideyo.client.activate', 'uses' => 'ClientController@getActivate'));    
    Route::post('client/{clientId}/activate', array('as' => 'hideyo.client.activate', 'uses' => 'ClientController@postActivate'));
    
    Route::get('client/{clientId}/de-activate', array('as' => 'hideyo.client.deactivate', 'uses' => 'ClientController@getDeActivate'));
    Route::post('client/{clientId}/de-activate', array('as' => 'hideyo.client.de-activate', 'uses' => 'ClientController@postDeActivate'));

    generateCrud('client/{clientId}/order', 'ClientOrderController');

    generateCrud('client/{clientId}/addresses', 'ClientAddressController');

    generateCrud('client', 'ClientController');

    Route::get('redirect/export', array('as' => 'admin.redirect.export', 'uses' => 'RedirectController@getExport'));
    Route::get('redirect/import', array('as' => 'admin.redirect.import', 'uses' => 'RedirectController@getImport'));
    Route::post('redirect/import', array('as' => 'admin.redirect.import', 'uses' => 'RedirectController@postImport'));

    Route::get('order/print/products', array('as' => 'hideyo.order.print.products', 'uses' => 'OrderController@getPrintOrders'));
    
    Route::get('order/print', array('as' => 'hideyo.order.print', 'uses' => 'OrderController@getPrint'));
    Route::post('order/print', array('as' => 'hideyo.order.print', 'uses' => 'OrderController@postPrint'));
  
    Route::post('order/print/download', array('as' => 'hideyo.order.download.print', 'uses' => 'OrderController@postDownloadPrint'));
  
    generateCrud('order', 'OrderController');

    generateCrud('order-status', 'OrderStatusController');

    Route::get('order-status-email-template/show-template/{id}', array('as' => 'order.status.email.template.ajax.show', 'uses' => 'OrderStatusEmailTemplateController@showAjaxTemplate'));

    generateCrud('order-status-email-template', 'OrderStatusEmailTemplateController');

    generateCrud('redirect', 'RedirectController');

    generateCrud('tax-rate', 'TaxRateController');

    generateCrud('general-setting', 'GeneralSettingController');

    generateCrud('sending-method', 'SendingMethodController');

    generateCrud('payment-method', 'PaymentMethodController');

    generateCrud('sending-payment-method-related', 'SendingPaymentMethodRelatedController');

    generateCrud('error', 'ErrorController');

    generateCrud('content/{contentId}/images', 'ContentImageController');

    Route::get('content/edit/{contentId}/seo', array('as' => 'hideyo.content.edit_seo', 'uses' => 'ContentController@editSeo'));

    generateCrud('content', 'ContentController');

    generateCrud('content-group', 'ContentGroupController');

    Route::get('content-group/edit/{contentGroupId}/seo', array('as' => 'hideyo.content-group.edit_seo', 'uses' => 'ContentGroupController@editSeo'));

    Route::get('news/refactor-images', array('as' => 'news.refactor.images', 'uses' => 'NewsController@refactorAllImages'));
 
    Route::get('news/re-directory-images', array('as' => 'news.re.directory.images', 'uses' => 'NewsController@reDirectoryAllImages'));
 
    Route::resource('news/{newsId}/images', 'NewsImageController');

    Route::get('news/edit/{newsId}/seo', array('as' => 'admin.news.edit_seo', 'uses' => 'NewsController@editSeo'));

    generateCrud('news', 'NewsController');

    generateCrud('news-group', 'NewsGroupController');

    Route::get('news-group/edit/{newsGroupId}/seo', array('as' => 'admin.news-group.edit_seo', 'uses' => 'NewsGroupController@editSeo'));

    generateCrud('faq', 'FaqItemController');

    Route::resource('html-block/{htmlBlockId}/copy', 'HtmlBlockController@copy');
    Route::get('html-block/change-active/{htmlBlockId}', array('as' => 'admin.html.block.change-active', 'uses' => 'HtmlBlockController@changeActive'));
 
    Route::post('html-block/{htmlBlockId}/copy', array('as' => 'html.block.store.copy', 'uses' => 'HtmlBlockController@storeCopy'));
 
    generateCrud('html-block', 'HtmlBlockController');

    generateCrud('coupon-group', 'CouponGroupController');

    generateCrud('coupon', 'CouponController');

    Route::post('order/update-status/{orderId}', array('as' => 'order.update-status', 'uses' => 'OrderController@updateStatus'));
 
    Route::get('order/update-sending-method/{sendingMethodId}', array('as' => 'order.update.sending.method', 'uses' => 'OrderController@updateSendingMethod'));
    Route::get('order/update-payment-method/{paymentMethodId}', array('as' => 'order.update.payment.method', 'uses' => 'OrderController@updatePaymentMethod'));

    Route::resource('order/{orderId}/download', 'OrderController@download');
    Route::resource('order/{orderId}/download-label', 'OrderController@downloadLabel');

    Route::get('order/update-billing-address/{addressId}', array('as' => 'order.update.billing.address', 'uses' => 'OrderController@updateClientBillAddress'));
    Route::get('order/update-delivery-address/{addressId}', array('as' => 'order.update.delivery.address', 'uses' => 'OrderController@updateClientDeliveryAddress'));

    Route::post('order/add-client', array('as' => 'admin.order.add-client', 'uses' => 'OrderController@addClient'));

    Route::post('order/add-product', array('as' => 'admin.order.add-product', 'uses' => 'OrderController@addProduct'));

    Route::get('order/update-amount-product/{productId}/{amount}', array('as' => 'admin.order.update.amount.product', 'uses' => 'OrderController@updateAmountProduct'));
 
    Route::get('order/change-product-combination/{productId}/{newProductId}', array('as' => 'admin.order.change.product.combination', 'uses' => 'OrderController@changeProductCombination'));

    Route::get('order/delete-product/{productId}', array('as' => 'order.delete-product', 'uses' => 'OrderController@deleteProduct'));

    Route::resource('invoice', 'InvoiceController');
    Route::resource('invoice/{invoiceId}/download', 'InvoiceController@download');

    generateCrud('attribute-group/{attributeGroupId}/attributes', 'AttributeController');

    generateCrud('attribute-group', 'AttributeGroupController');

    Route::resource('extra-field/{extraFieldId}/values', 'ExtraFieldDefaultValueController');
    
    generateCrud('extra-field', 'ExtraFieldController');

    Route::get('product/refactor-images', array('as' => 'product.refactor-images', 'uses' => 'ProductController@refactorAllImages'));
    Route::get('product/re-directory-images', array('as' => 'product.re-directory-images', 'uses' => 'ProductController@reDirectoryAllImages'));

    Route::post('product/export', array('as' => 'hideyo.product.export', 'uses' => 'ProductController@postExport'));

    Route::get('product/export', array('as' => 'hideyo.product.export', 'uses' => 'ProductController@getExport'));

    Route::get('product/rank', array('as' => 'hideyo.product.ranking', 'uses' => 'ProductController@getRank'));

    generateCrud('product', 'ProductController');

    Route::get('product/edit/{productId}/price', array('as' => 'hideyo.product.edit_price', 'uses' => 'ProductController@editPrice'));
    Route::get('product/change-active/{productId}', array('as' => 'hideyo.product.change-active', 'uses' => 'ProductController@changeActive'));
    Route::get('product/change-amount/{productId}/{amount}', array('as' => 'hideyo.product.change-amount', 'uses' => 'ProductController@changeAmount'));
    Route::get('product/change-rank/{productId}/{rank}', array('as' => 'hideyo.product.change-rank', 'uses' => 'ProductController@changeRank'));
  
    Route::get('product/edit/{productId}/seo', array('as' => 'hideyo.product.edit_seo', 'uses' => 'ProductController@editSeo'));
    Route::resource('product/{productId}/images', 'ProductImageController');
    Route::resource('product/{productId}/product-amount-option', 'ProductAmountOptionController');
    Route::resource('product/{productId}/product-amount-series', 'ProductAmountSeriesController');

    Route::resource('product/{productId}/copy', 'ProductController@copy');

    Route::resource('product/{productId}/product-combination', 'ProductCombinationController');

    Route::get('product/{productId}/product-combination/change-amount-attribute/{id}/{amount}', array('as' => 'hideyo.product.change-amount', 'uses' => 'ProductCombinationController@changeAmount'));
 
    Route::post('product/{productId}/copy', array('as' => 'product.store-copy', 'uses' => 'ProductController@storeCopy'));
    Route::resource('product/{productId}/product-extra-field-value', 'ProductExtraFieldValueController');
    Route::resource('product/{productId}/related-product', 'ProductRelatedProductController');
    Route::get('product-category/refactor-images', array('as' => 'product-category.refactor-images', 'uses' => 'ProductCategoryController@refactorAllImages'));
    Route::get('product-category/re-directory-images', array('as' => 'product-category.re-directory-images', 'uses' => 'ProductCategoryController@reDirectoryAllImages'));

    generateCrud('brand/{brandId}/images', 'BrandImageController');
 
    Route::get('brand/edit/{brandId}/seo', array('as' => 'hideyo.brand.edit_seo', 'uses' => 'BrandController@editSeo'));
 
    generateCrud('brand', 'BrandController');

    Route::get('product-category/change-active/{productCategoryId}', array('as' => 'hideyo.product-category.change-active', 'uses' => 'ProductCategoryController@changeActive'));

    Route::get('product_category/get_ajax_categories', array('as' => 'hideyo.product-category.ajax_categories', 'uses' => 'ProductCategoryController@ajaxCategories'));
    Route::get('product_category/get_ajax_category/{id}', array('as' => 'hideyo.product-category.ajax_category', 'uses' => 'ProductCategoryController@ajaxCategory'));
 
    Route::get('product_category/edit/{productCategoryId}/hightlight', array('as' => 'hideyo.product-category.edit.hightlight', 'uses' => 'ProductCategoryController@editHighlight'));

    Route::resource('product-category/{productCategoryId}/images', 'ProductCategoryImageController');
    Route::get('product_category/edit/{productCategoryId}/seo', array('as' => 'hideyo.product-category.edit_seo', 'uses' => 'ProductCategoryController@editSeo'));

    Route::get('product_category/ajax-root-tree', array('as' => 'hideyo.product-category.ajax-root-tree', 'uses' => 'ProductCategoryController@ajaxRootTree'));
    Route::get('product_category/ajax-children-tree', array('as' => 'hideyo.product-category.ajax-children-tree', 'uses' => 'ProductCategoryController@ajaxChildrenTree'));
    Route::get('product_category/ajax-move-node', array('as' => 'hideyo.product-category.ajax-move-node', 'uses' => 'ProductCategoryController@ajaxMoveNode'));

    Route::get('product_category/tree', array('as' => 'hideyo.product-category.tree', 'uses' => 'ProductCategoryController@tree'));

    generateCrud('product-category', 'ProductCategoryController');

    generateCrud('product-tag-group', 'ProductTagGroupController');

    generateCrud('user', 'UserController');

    Route::get('profile/shop/change/{shopId}', array('as' => 'change.language.profile', 'uses' => 'UserController@changeShopProfile'));
    Route::get('profile', array('as' => 'edit.profile', 'uses' => 'UserController@editProfile'));
    Route::post('profile', array('as' => 'update.profile', 'uses' => 'UserController@updateProfile'));
    Route::post('profile_language', array('as' => 'update.language', 'uses' => 'UserController@updateLanguage'));
});

Route::group(['prefix' => config()->get('hideyo.route_prefix').'/admin', 'namespace' => 'Hideyo\Backend\Controllers', 'middleware' => ['web']], function () {
    Route::get('/security/login', 'AuthController@getLogin');
    Route::post('/security/login', 'AuthController@postLogin');
    Route::get('/security/logout', array('as' => 'hideyo.security.logout', 'uses' => 'AuthController@getLogout'));
});