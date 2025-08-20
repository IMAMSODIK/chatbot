@extends('template')

@section('content')
    <div class="page-body">
        <div class="container-fluid mt-4">
            <div class="page-title">
                <div class="row mt-4">
                    <div class="col-6">
                        <h4>User</h4>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <!-- Zero Configuration  Starts-->
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive custom-scrollbar">
                                <table class="display" id="basic-1">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%; font-size: 18px" class="text-center">No. </th>
                                            <th style="font-size: 18px" class="text-center">Nama</th>
                                            <th style="width: 15%; font-size: 18px" class="text-center">Role</th>
                                            <th style="width: 30%; font-size: 18px" class="text-center">Identitas User</th>
                                            <th style="width: 10%; font-size: 18px" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $index = 1; @endphp
                                        @foreach ($users as $user)
                                            <tr>
                                                <td style="font-size: 18px" class="text-center align-middle">
                                                    {{ $index++ }}</td>
                                                <td class="align-middle" style="font-size: 18px">
                                                    {{ $user->name }} <br>
                                                    <small>{{ $user->email }}</small>
                                                </td>
                                                <td class="text-center align-middle" style="font-size: 18px">
                                                    {{ $user->role }}</td>
                                                <td class="text-center align-middle" style="font-size: 18px">
                                                    <img src="{{ asset('storage') . '/' . $user->file_identitas }}"
                                                        width="150px" class="img-thumbnail user-identitas-img"
                                                        style="cursor:pointer" data-bs-toggle="modal"
                                                        data-bs-target="#identitasModal"
                                                        data-img="{{ $user->file_identitas }}">
                                                </td>
                                                <td class="text-center align-middle">
                                                    <ul class="action d-flex justify-content-center">
                                                        <li class="delete btn btn-{{ $user->status ? 'success' : 'danger' }}"
                                                            data-id="{{ $user->id }}">
                                                            {{ $user->status ? 'Aktif' : 'Tidak Aktif' }}
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="identitasModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Detail Gambar</h5>
                    <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img id="gambar" width="100%" alt="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-alert" id="alert" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenter1"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-toggle-wrapper">
                        <ul class="modal-img">
                            <li> <img id="alert-image"></li>
                        </ul>
                        <h4 class="text-center pb-2" id="alert-title"></h4>
                        <p class="text-center" id="alert-message"></p>
                        <button class="btn btn-secondary d-flex m-auto" id="close-alert" type="button"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenter1"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-toggle-wrapper">
                        <ul class="modal-img">
                            <li> <img id="alert-image" src="{{ asset('own_assets/icon/confirm.gif') }}" width="300px">
                            </li>
                        </ul>
                        <h4 class="text-center pb-2" id="alert-title">Verifikasi Data</h4>
                        <p class="text-center" id="alert-message">Apakah anda yakin ingin {{ $user->status ? 'menonaktifkan' : 'mengaktifkan' }} pengguna ini?</p>
                        <div class="row">
                            <div class="col-md-6 d-flex justify-content-end">
                                <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Cancel</button>
                            </div>
                            <div class="col-md-6 d-flex justify-content-start">
                                <button class="btn btn-danger" id="delete-confirmed" type="button"
                                    data-bs-dismiss="modal">{{ $user->status ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('own_script')
    <script src="{{ asset('own_assets/scripts/user.js') }}"></script>
@endsection
