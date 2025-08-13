@extends('template')

@section('content')

    <div class="page-body">
        <div class="container-fluid" style="margin-top: 50px">
            <div class="page-title">
                <div class="row">
                    <div class="col-6">
                        <h4>Dashboard</h4>
                    </div>
                </div>
            </div>

            @if (session()->get('role') == 'admin')
                <!-- Container-fluid starts-->
                <div class="container-fluid">
                    <div class="row size-column">
                        <div class="col-xxl-12 box-col-12">
                            <div class="row">

                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-xl-3 col-sm-6">
                                            <div class="card o-hidden small-widget">
                                                <div class="card-body total-project border-b-primary border-2"><span
                                                        class="f-light f-w-500 f-14">Total Peserta</span>
                                                    <div class="project-details">
                                                        <div class="project-counter">
                                                            <h2 class="f-w-600">200</h2><span
                                                                class="f-12 f-w-400">(Peserta)</span>
                                                        </div>
                                                        <div class="product-sub bg-primary-light">
                                                            <svg class="invoice-icon">
                                                                <use
                                                                    href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#color-swatch') }}">
                                                                </use>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <ul class="bubbles">
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-sm-6">
                                            <div class="card o-hidden small-widget">
                                                <div class="card-body total-Progress border-b-warning border-2"> <span
                                                        class="f-light f-w-500 f-14">Soal</span>
                                                    <div class="project-details">
                                                        <div class="project-counter">
                                                            <h2 class="f-w-600">3000</h2><span class="f-12 f-w-400">(Soal)
                                                            </span>
                                                        </div>
                                                        <div class="product-sub bg-warning-light">
                                                            <svg class="invoice-icon">
                                                                <use
                                                                    href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#tick-circle') }}">
                                                                </use>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <ul class="bubbles">
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-sm-6">
                                            <div class="card o-hidden small-widget">
                                                <div class="card-body total-upcoming"><span
                                                        class="f-light f-w-500 f-14">Total
                                                        Sesi</span>
                                                    <div class="project-details">
                                                        <div class="project-counter">
                                                            <h2 class="f-w-600">14</h2><span class="f-12 f-w-400">(Sesi)
                                                            </span>
                                                        </div>
                                                        <div class="product-sub bg-light-light">
                                                            <svg class="invoice-icon">
                                                                <use
                                                                    href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#edit-2') }}">
                                                                </use>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <ul class="bubbles">
                                                        <li class="bubble"> </li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-sm-6">
                                            <div class="card o-hidden small-widget">
                                                <div class="card-body total-Complete border-b-secondary border-2"><span
                                                        class="f-light f-w-500 f-14">Total Pendapatan</span>
                                                    <div class="project-details">
                                                        <div class="project-counter">
                                                            <h2 class="f-w-600">Rp. 500.000</h2><span
                                                                class="f-12 f-w-400">(Rupiah) </span>
                                                        </div>
                                                        <div class="product-sub bg-secondary-light">
                                                            <svg class="invoice-icon">
                                                                <use
                                                                    href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#add-square') }}">
                                                                </use>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <ul class="bubbles">
                                                        <li class="bubble"> </li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"> </li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"> </li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"></li>
                                                        <li class="bubble"> </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="text-center bg-dark text-white py-2 mb-4" style="border-radius: 15px; font-size: 18px;">
                          DAFTAR REALISASI PENERIMAAN PENDAPATAN BISNIS UIN SUMATERA UTARA MEDAN TAHUN {{ session('tahun') }}
                      </div> --}}

                                {{-- <div class="row justify-content-center"> 
                          <div class="col-md-4 col-sm-6">
                              <div class="card o-hidden small-widget">
                                  <div class="card-body total-project border-b-primary border-2">
                                      <span class="f-light f-w-500 f-14">Total Penerimaan</span>
                                      <div class="project-details">
                                          <div class="project-counter">
                                              <h2 class="f-w-600">Rp. {{ $totalPenerimaan }}</h2><span class="f-12 f-w-400">(Rapat)</span>
                                          </div>
                                          <div class="product-sub bg-primary-light">
                                              <svg class="invoice-icon">
                                                  <use href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#color-swatch') }}"></use>
                                              </svg>
                                          </div>
                                      </div>
                                      <ul class="bubbles">
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                      </ul>
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-4 col-sm-6">
                              <div class="card o-hidden small-widget">
                                  <div class="card-body total-Progress border-b-warning border-2">
                                      <span class="f-light f-w-500 f-14">Total Forcasting</span>
                                      <div class="project-details">
                                          <div class="project-counter">
                                              <h2 class="f-w-600">Rp. {{ $totalForcasting }}</h2><span class="f-12 f-w-400">(Rapat)</span>
                                          </div>
                                          <div class="product-sub bg-warning-light">
                                              <svg class="invoice-icon">
                                                  <use href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#tick-circle') }}"></use>
                                              </svg>
                                          </div>
                                      </div>
                                      <ul class="bubbles">
                                          <li class="bubble"></li>
                                          <li class="bubble"></li>
                                          <li class="bubble"></li>
                                          <li class="bubble"></li>
                                          <li class="bubble"></li>
                                          <li class="bubble"></li>
                                          <li class="bubble"></li>
                                          <li class="bubble"></li>
                                          <li class="bubble"></li>
                                          <li class="bubble"></li>
                                      </ul>
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-4 col-sm-6">
                              <div class="card o-hidden small-widget">
                                  <div class="card-body total-Complete border-b-secondary border-2">
                                      <span class="f-light f-w-500 f-14">Selisih</span>
                                      <div class="project-details">
                                          <div class="project-counter">
                                              <h2 class="f-w-600">Rp. {{ $selisih }}</h2><span class="f-12 f-w-400">(Rapat)</span>
                                          </div>
                                          <div class="product-sub bg-secondary-light">
                                              <svg class="invoice-icon">
                                                  <use href="{{ asset('dashboard_assets/assets/svg/icon-sprite.svg#add-square') }}"></use>
                                              </svg>
                                          </div>
                                      </div>
                                      <ul class="bubbles">
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                        <li class="bubble"></li>
                                      </ul>
                                  </div>
                              </div>
                          </div>
                      </div> --}}

                                {{-- <div class="col-md-8">
                          <div class="card">
                              <div class="card-header d-flex justify-content-between align-items-center">
                                  <h4>Statistik dan Forcasting Layanan</h4>
                                  <a href="/monitor-pendapatan" class="text-end text-decoration-none" style="font-weight: bolder"
                                  onmouseover="this.style.fontWeight='bold'" onmouseout="this.style.fontWeight='600'">Lihat Semua</a>
                              </div>
                              <div class="card-body pt-0">
                                  <div class="table-responsive custom-scrollbar" style="overflow-x: auto; padding:0px;">
                                      <table class="today-working-table w-100" style="table-layout: fixed; border-collapse: separate; border-spacing: 0 10px;">
                                          <thead>
                                              <tr>
                                                  <th style="width: 6%; text-align: center; padding: 10px; background-color: #f8f9fa;">No</th>
                                                  <th style="width: 12%; text-align: center; padding: 10px; background-color: #f8f9fa;">Akun</th>
                                                  <th style="width: 15%; text-align: center; padding: 10px; background-color: #f8f9fa;">Uraian Akun</th>
                                                  <th style="width: 19%; text-align: center; padding: 10px; background-color: #f8f9fa;">Uraian Layanan Bisnis</th>
                                                  <th style="width: 16%; text-align: center; padding: 10px; background-color: #f8f9fa;">Target Penerimaan</th>
                                                  <th style="width: 15%; text-align: center; padding: 10px; background-color: #f8f9fa;">Realisasi</th>
                                                  <th style="width: 10%; text-align: center; padding: 10px; background-color: #f8f9fa;">% Kinerja</th>
                                                  <th style="width: 16%; text-align: center; padding: 10px; background-color: #f8f9fa;">Kekurangan Target</th>
                                              </tr>
                                          </thead>
                                          <tbody>
                                              @php $index = 1; @endphp
                                              @foreach ($forcastings as $f)
                                                  <tr style="background-color: #ffffff; box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1); border-radius: 8px;">
                                                      <td style="text-align: center; padding: 10px;">{{ $index++ }}</td>
                                                      <td style="text-align: center; padding: 10px;">{{ $f->akun }}</td>
                                                      <td style="text-align: center; padding: 10px;">{{ $f->uraian_akun }}</td>
                                                      <td style="text-align: center; padding: 10px;">{{ $f->uraian_layanan }}</td>
                                                      <td style="text-align: right; padding: 10px;">Rp.{{ number_format($f->target_penerimaan, 0, ',', '.') }}</td>
                                                      <td style="text-align: right; padding: 10px;">Rp.{{ number_format($f->total_realisasi, 0, ',', '.') }}</td>
                                                      <td style="text-align: center; padding: 10px;">{{ number_format($f->capaian, 2, ',', '.') }}%</td>
                                                      <td style="text-align: right; padding: 10px;">Rp.{{ number_format($f->kekurangan, 0, ',', '.') }}</td>
                                                  </tr>
                                              @endforeach
                                          </tbody>
                                      </table>
                                  </div>
                                  
                              </div>
                          </div>
                      </div> --}}
                                {{-- <div class="col-md-4">
                          <div class="card">
                              <div class="card-header card-no-border total-revenue d-flex justify-content-between align-items-center">
                                  <h5>Hari ini, {{ $tanggalHariIni }}</h5>
                                  <a href="/booking-order" class="text-end text-decoration-none"
                                      onmouseover="this.style.fontWeight='bold'" onmouseout="this.style.fontWeight='600'">Lihat Semua</a>
                              </div>
                              <div class="card-body pt-0">
                                  <div class="table-responsive custom-scrollbar" style="overflow-x: auto;">
                                      <table class="today-working-table w-100" style="table-layout: fixed;">
                                          <thead>
                                              <tr>
                                                  <th style="width: 60%; vertical-align: middle;">Layanan / Acara</th>
                                                  <th style="width: 30%; vertical-align: middle;">Pemakai Jasa</th>
                                                  <th class="text-end" style="width: 30%; vertical-align: middle; padding-left: 30px;">Status</th>
                                              </tr>
                                          </thead>
                                          <tbody>
                                              @foreach ($bookingOrders as $order)
                                                  <tr>
                                                      <td style="white-space: normal; word-wrap: break-word; vertical-align: top; padding-top: 20px;">
                                                          <p class="f-w-500 f-14">
                                                              {{ $order->layanan->uraian_layanan ?? '-' }} / {{ $order->event ?? '-' }}
                                                          </p>
                                                      </td>
                                                      <td style="white-space: normal; word-wrap: break-word; vertical-align: top; padding-top: 20px;">
                                                          <p class="f-w-500 f-14">{{ $order->customer->nama_customer ?? '-' }}</p>
                                                      </td>
                                                      <td class="text-end" style="vertical-align: top; padding-top: 20px; padding-left: 30px;">
                                                          <div class="{{ $order->statusColor }} product-sub badge rounded-pill">
                                                              <span>{{ $order->statusText }}</span>
                                                          </div>
                                                      </td>
                                                  </tr>
                                              @endforeach
                                          </tbody>
                                      </table>
                                  </div>
                              </div>
                          </div>
                      </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Container-fluid Ends-->
            @else
                <!-- Container-fluid starts-->
                <div class="container-fluid">
                    <div class="row">
                    </div>
                </div>
                <!-- Container-fluid Ends-->

                <div class="modal fade bd-example-modal-lg" id="tambah-data-modal" tabindex="-1" role="dialog"
                    aria-labelledby="myExtraLargeModal" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myExtraLargeModal">Tambah Sesi</h4>
                                <button class="btn-close py-0" type="button" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body dark-modal">
                                <div class="card">
                                    <form class="form theme-form dark-inputs">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="nama">Nama Sesi</label>
                                                        <input class="form-control input-air-primary" id="nama"
                                                            type="text" placeholder="Masukkan nama sesi">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="deskripsi">Deskripsi</label>
                                                        <textarea class="form-control input-air-primary" id="deskripsi" placeholder="Deskripsi" cols="30"
                                                            rows="10"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="harga">Harga Sesi</label>
                                                        <input class="form-control input-air-primary" id="harga"
                                                            type="text" placeholder="Masukkan harga sesi">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="tipe_soal">Tipe Soal</label>
                                                        <select class="form-control input-air-primary" id="tipe_soal">
                                                            <option value="acak">Acak Soal</option>
                                                            <option value="pilih">Pilih Soal</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label class="form-label" for="dt_mulai">Waktu
                                                                    Mulai</label>
                                                                <input class="form-control input-air-primary"
                                                                    id="dt_mulai" type="datetime-local">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label" for="dt_selesai">Waktu
                                                                    Selesai</label>
                                                                <input class="form-control input-air-primary"
                                                                    id="dt_selesai" type="datetime-local">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label" for="durasi">Durasi
                                                                    Sesi</label>
                                                                <input class="form-control input-air-primary"
                                                                    id="durasi" type="text" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value=""
                                                            id="tampil_hasil">
                                                        <label class="form-check-label" for="tampil_hasil">
                                                            Tampilkan Hasil
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value=""
                                                            id="tampil_ranking">
                                                        <label class="form-check-label" for="tampil_ranking">
                                                            Tampilkan Perankingan
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer text-end">
                                            <input class="btn btn-light" type="reset" id="cancel-add" value="Cancel">
                                            <button class="btn btn-primary me-3" type="button"
                                                id="store">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade bd-example-modal-lg" id="edit-data-modal" tabindex="-1" role="dialog"
                    aria-labelledby="myExtraLargeModal" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myExtraLargeModal">Edit Rapat</h4>
                                <button class="btn-close py-0" type="button" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body dark-modal">
                                <div class="card">
                                    <form class="form theme-form dark-inputs">
                                        <input type="hidden" name="" id="id">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="edit_nama">Nama Sesi</label>
                                                        <input class="form-control input-air-primary" id="edit_nama"
                                                            type="text" placeholder="Masukkan nama sesi">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="edit_deskripsi">Deskripsi</label>
                                                        <textarea class="form-control input-air-primary" id="edit_deskripsi" placeholder="Deskripsi" cols="30"
                                                            rows="10"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="edit_harga">Harga Sesi</label>
                                                        <input class="form-control input-air-primary" id="edit_harga"
                                                            type="text" placeholder="Masukkan harga sesi">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="edit_tipe_soal">Tipe Soal</label>
                                                        <select class="form-control input-air-primary"
                                                            id="edit_tipe_soal">
                                                            <option value="acak">Acak Soal</option>
                                                            <option value="pilih">Pilih Soal</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label class="form-label" for="edit_dt_mulai">Waktu
                                                                    Mulai</label>
                                                                <input class="form-control input-air-primary"
                                                                    id="edit_dt_mulai" type="datetime-local">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label" for="edit_dt_selesai">Waktu
                                                                    Selesai</label>
                                                                <input class="form-control input-air-primary"
                                                                    id="edit_dt_selesai" type="datetime-local">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label" for="edit_durasi">Durasi
                                                                    Sesi</label>
                                                                <input class="form-control input-air-primary"
                                                                    id="edit_durasi" type="text" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value=""
                                                            id="edit_tampil_hasil">
                                                        <label class="form-check-label" for="edit_tampil_hasil">
                                                            Tampilkan Hasil
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value=""
                                                            id="edit_tampil_ranking">
                                                        <label class="form-check-label" for="edit_tampil_ranking">
                                                            Tampilkan Perankingan
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer text-end">
                                            <input class="btn btn-light" type="reset" id="cancel-edit" value="Cancel">
                                            <button class="btn btn-primary me-3" type="button"
                                                id="update">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade bd-example-modal-lg" id="show-surat-modal" tabindex="-1" role="dialog"
                    aria-labelledby="myExtraLargeModal" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myExtraLargeModal">Surat Rapat</h4>
                                <button class="btn-close py-0" type="button" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body dark-modal">
                                <div class="card">
                                    <form class="form theme-form dark-inputs">
                                        <input type="hidden" name="" id="id">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3" id="pdf-container">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer text-end">
                                            <input class="btn btn-light" type="reset" id="cancel-surat"
                                                value="Cancel">
                                            <button class="btn btn-primary me-3" type="button"
                                                id="update">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade bd-example-modal-lg" id="upload-bb" tabindex="-1" role="dialog"
                    aria-labelledby="myExtraLargeModal" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myExtraLargeModal">Bukti Pembayaran</h4>
                                <button class="btn-close py-0" type="button" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body dark-modal">
                                <div class="card">
                                    <form class="form theme-form dark-inputs">
                                        <input type="hidden" name="" id="sesi_id">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="bb">Upload Bukti
                                                            Bayar</label>
                                                        <input class="form-control input-air-primary" id="bb"
                                                            type="file">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer text-end">
                                            <input class="btn btn-light" type="reset" id="cancel-upload"
                                                value="Cancel">
                                            <button class="btn btn-primary me-3" type="button"
                                                id="upload">Kirim</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade modal-alert" id="alert" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalCenter1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="modal-toggle-wrapper">
                                    <ul class="modal-img">
                                        <li> <img id="alert-image"></li>
                                    </ul>
                                    <h4 class="text-center pb-2" id="alert-title"></h4>
                                    <p class="text-center" id="alert-message"></p>
                                    <button class="btn btn-secondary d-flex m-auto" type="button"
                                        data-bs-dismiss="modal" id="close-alert">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="confirm" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalCenter1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="modal-toggle-wrapper">
                                    <ul class="modal-img">
                                        <li> <img id="alert-image" src="{{ asset('own_assets/icon/confirm.gif') }}"
                                                width="300px">
                                        </li>
                                    </ul>
                                    <h4 class="text-center pb-2" id="alert-title">Hapus Data</h4>
                                    <p class="text-center" id="alert-message">Apakah anda yakin ingin menghapus data?</p>
                                    <div class="row">
                                        <div class="col-md-6 d-flex justify-content-end">
                                            <button class="btn btn-primary" type="button"
                                                data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                        <div class="col-md-6 d-flex justify-content-start">
                                            <button class="btn btn-danger" id="delete-confirmed" type="button"
                                                data-bs-dismiss="modal">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @section('own_script')
                    <script src="{{ asset('own_assets/scripts/sesi.js') }}"></script>
                @endsection
            @endif
        </div>
    </div>
@endsection
