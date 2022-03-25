@extends('layouts.vendor')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/front/css/toastr.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/plugins/slim-select/slimselect.min.css')}}">
@endsection

@section('content')

            <div class="content-area">

              <div class="mr-breadcrumb">
                <div class="row">
                  <div class="col-lg-12">
                      <h4 class="heading">{{ __('Add New Delivery Fee') }} <a class="add-btn" href="{{route('vendor-delivery-fees')}}"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a></h4>
                      <ul class="links">
                        <li>
                          <a href="{{ route('vendor-dashboard') }}">{{ __('Dashboard') }} </a>
                        </li>
                        <li>
                          <a href="javascript:;">{{ __('Delivery Settings') }}</a>
                        </li>
                        <li>
                          <a href="{{ route('vendor-delivery_fee-create') }}">{{ __('Add New Delivery Fee') }}</a>
                        </li>
                      </ul>
                  </div>
                </div>
              </div>

              <div class="add-product-content">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="product-description">
                      <div class="body-area">
                        <div class="gocover" style="background: url({{asset('assets/images/'.$gs->admin_loader)}}) no-repeat scroll center center rgba(45, 45, 45, 0.5);"></div>
                        @include('includes.vendor.form-both') 
                      <form id="geniusform" action="{{route('vendor-delivery_fee-update')}}" method="POST">
                        {{csrf_field()}}

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Class Name') }} *</h4>
                                <p class="sub-heading">{{ __('(In Any Language)') }}</p>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <input type="text" class="input-field" id="class_name" name="class_name" placeholder="{{ __('Enter Class Name') }}" required="" value="">
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Provinces') }} *</h4>
                            </div>
                          </div>
                          <div class="col-lg-7">						  		
                              <select id="provinces" name="provinces" multiple>                                
								@foreach($provinces as $province)
                                <option value="{{$province->id}}">{{ $province->province_name }}</option>                                
								@endforeach
                              </select>
							  <input type="hidden" name="province_ids"/>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Delivery Fee') }} *</h4>
                            </div>
                          </div>
                          <div class="col-lg-7">
							<div class="input-group mb-3">
								<input type="number" min="0" id="delivery_fee" name="delivery_fee" class="form-control">
								<div class="input-group-append">
									<span class="input-group-text">{{$sign->name}}</span>
								</div>
							</div>	
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Description') }}</h4>
								<p class="sub-heading">{{ __('(This field is optional)') }}</p>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            	<textarea class="input-field" name="description" placeholder="{{ __('Enter Description') }}"></textarea>
                          </div>
                        </div>
                        <br>
                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
								<input type="hidden" name="id" value="0"/>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <button class="addProductSubmit-btn btn-submit" type="button">{{ __('Create Fee') }}</button>
                          </div>
                        </div>
                      </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

@endsection

@section('scripts')
<script src="{{asset('assets/vendor/plugins/slim-select/slimselect.min.js')}}"></script>
<script src="{{asset('assets/front/js/toastr.js')}}"></script>
<script type="text/javascript">

	new SlimSelect({
		select: '#provinces'
	});	

	$('.btn-submit').on('click', function() {
		var provinces = $('#provinces').val();
		var msg = '';
		if($("#class_name").val()=='') {
			msg += 'Please Enter Class Name';
		}
		if($('#delivery_fee').val()=='' || Number($('#delivery_fee').val())==0) {
			if(msg!='') msg += '<br>';
			msg += 'Please Enter Delivery Fee';
		}
		if(!provinces) {
			if(msg!='') msg += '<br>';
			msg += 'Please Select Provinces';			
		}
		if(msg != '') {
			toastr.error(msg);
			return false;
		}
		$('input[name="province_ids"]').val(provinces.join(','));
		$('#geniusform').submit();
	});


</script>

@endsection

