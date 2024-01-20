@extends('template_back.content')
@section('title', 'Form Input Pengguna')
@section('content')


    <!-- container opened -->
<div class="container">

    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">Form Input Pengguna</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('data_pengguna')}}">Data Pengguna</a></li>
                    <li class="breadcrumb-item text-white active">Form Input Pengguna</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- /breadcrumb -->
    <div class="row row-sm">
        <div class="col-xl-12 col-lg-12 col-sm-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="main-content-label mg-b-5">
                        Form Input Pengguna
                    </div>
                    <p class="mg-b-20">Silahkan isi form di bawah ini dengan lengkap.</p>
                    <!-- message info -->
                    @include('_component.message')
                    <div class="pd-10 pd-sm-20 bg-gray-100">
                        <form action="{{ route('data_pengguna.create') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row row-xs align-items-center mg-b-20">
                                        <div class="col-md-3">
                                            <label class="form-label mg-b-0">Name <span class="tx-danger">*</span></label>
                                        </div>
                                        <div class="col-md-9 mg-t-5 mg-md-t-0">
                                            <input class="form-control" placeholder="Masukan Nama" type="text" name="name" value="{{old('name')}}" required>
                                        </div>
                                    </div>
                                    <div class="row row-xs align-items-center mg-b-20">
                                        <div class="col-md-3">
                                            <label class="form-label mg-b-0">Email </label>
                                        </div>
                                        <div class="col-md-9 mg-t-5 mg-md-t-0">
                                            <input class="form-control" placeholder="Masukan Email" type="email" name="email" value="{{old('email')}}">
                                        </div>
                                    </div>
                                    {{-- <div class="row row-xs align-items-center mg-b-20">
                                        <div class="col-md-3">
                                            <label class="form-label mg-b-0">Kategori </label>
                                        </div>
                                        <div class="col-md-9 mg-t-5 mg-md-t-0">
                                            <select class="form-control select2" name="kategori_id">
                                                @php 
                                                $dbKategori = DB::table('tm_kategoribarang')->select('*')->orderBy('nama','ASC')->get(); 
                                                @endphp
                                                <option value="">=== pilih ===</option>
                                                @foreach($dbKategori as $key => $val)
                                                <option value="{{$val->id}}" @if(old("kategori_id")==$val->id) selected @endif>{{$val->nama}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="row row-xs align-items-center mg-b-20">
                                        <div class="col-md-3">
                                            <label class="form-label mg-b-0">Password Baru</label>
                                        </div>
                                        <div class="col-md-9 mg-t-5 mg-md-t-0">
                                            <input class="form-control" name='password' placeholder="Masukan Password" type="password" value="{{old('password')}}">
                                        </div>
                                    </div>
                                    <div class="row row-xs align-items-center mg-b-20">
                                        <div class="col-md-3">
                                            <label class="form-label mg-b-0"> Role</label>
                                        </div>
                                        <div class="col-md-9 mg-t-5 mg-md-t-0">
                                         <select class="form-control" name="namerole" id="">
                                            <option  value="" selected disabled> -- Pilih Role --</option>
                                            <option  value="administrator">Administrator</option>
                                            <option  value="operator">Operator</option>
                                         </select>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="row row-xs align-items-center mg-b-20">
                                    <div class="col-md-3">
                                        <label class="form-label mg-b-0">Gambar Pengguna </label>
                                    </div>
                                    <div class="col-md-9 mg-t-5 mg-md-t-0">
                                        <input class="form-control" name='img' placeholder="Masukan Password" type="file" value="{{old('img')}}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        </div>
                        <button type="submit" class="float-right btn btn-primary pd-x-30 mg-e-5 mg-t-5">
                            <i class='fa fa-save'></i> Simpan</button>
                        <a href="{{route('data_pengguna')}}" class="btn btn-secondary pd-x-30 mg-t-5">
                            <i class='fa fa-chevron-left'></i> Kembali</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
<!-- /container -->
                              

        <script>
            $(function() {
                //formplugin
                $('.select2').select2();
                $('#datepickerA,#datepickerB').datepicker({
                    format: 'dd/mm/yyyy', 
                    autoclose: true,
                    todayHighlight: true,
                });
                $(".numberonly").on('input', function(e) {
                    $(this).val($(this).val().replace(/[^0-9]/g, ''));
                });
            });

            function number_format(number, decimals, decPoint, thousandsSep){
                number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
                var n = !isFinite(+number) ? 0 : +number
                var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
                var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
                var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
                var s = ''
                var toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec)
                return '' + (Math.round(n * k) / k)
                    .toFixed(prec)
                }
                // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
                s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
                if (s[0].length > 3) {
                    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
                }
                if ((s[1] || '').length < prec) {
                    s[1] = s[1] || ''
                    s[1] += new Array(prec - s[1].length + 1).join('0')
                }
                return s.join(dec)
            }
        </script>
    

@endsection