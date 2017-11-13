@extends('frontend._layouts.default')

@section('main')


<div class="row">
    <div class="col-sm-3 col-md-2">
        <ol class="breadcrumb">
            <li><a href="/">Home</a></li>
            @if($category->ancestors()->count())
            @foreach ($category->ancestors()->get() as $anchestor)
            <li><a href="/{{ $anchestor->slug }}">{{ $anchestor->title }}</a></li>
            @endforeach
            @endif
            <li><a href="/{{ $category->slug }}">{{ $category->title }}</a></li>
        </ol>
    </div>
</div>


<div class="row">
	<div class="col-sm-3 col-md-2">
		<p>categories</p>
	</div>
</div>


@if ($childrenProductCategories)

@foreach ($childrenProductCategories->chunk(3) as $categories)
<div class="row ">
    @foreach ($categories as $category)
    <div class="col-sm-3 col-md-2">
        <div class="category-block">
            <div class="row">
                <a href="/{{ $category['slug'] }}" title="ga naar {{ $category['title'] }}">
                    <div class="col-sm-3 col-md-2">

                        <div class="image"> 
                            @if($category->productCategoryImages->count())
                            <img src="/files/product_category/200x200/{!! $category->productCategoryImages->first()->product_category_id !!}/{!! $category->productCategoryImages->first()->file !!}" class="img-responsive main-photo" alt="" />          
                            <div class="overlay"></div>
                            @endif
                        </div>

                    </div>
                    
                    <div class="col-sm-3 col-md-2">
                        <div class="category-block-content">
                            <h3>{{ str_limit($category['title'],21) }}</h3>
                            <p class="hide-for-medium-only">{{ $category->short_description  }}</p>

                        </div>
                    </div>
                </a>
            </div>
        </div>            
    </div>
    @endforeach
</div>
@endforeach
@endif


@stop