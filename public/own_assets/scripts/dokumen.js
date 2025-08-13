let table = $("#basic-1").DataTable();
let jawabIndex = 1;
let modal = "", target = "", id_ctr = 0;

$("#cancel-edit").on("click", function () {
    closeModal($("#edit-data-modal"));
})

$("#cancel-add").on("click", function () {
    closeModal($("#tambah-data-modal"));
})

function alertModal(status, message = null) {
    if (status) {
        $("#alert-image").attr("src", '../../dashboard_assets/assets/images/gif/dashboard-8/successful.gif');
        $("#alert-message").text("Success");
        $("#alert-message").text(message);
    } else {
        $("#alert-image").attr("src", '../../dashboard_assets/assets/images/gif/danger.gif');
        $("#alert-message").text("Gagal");
        $("#alert-message").text(message);
    }

    $("#alert").modal('show');
}

function openModal(modal) {
    $(modal).modal('show');
}

$("#tambah-data").on("click", function () {
    $("#tambah-data-modal").modal("show");
});

$('#store').on('click', function () {
    var formData = new FormData();
    var files = $('#file_path')[0].files;

    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }
    formData.append('_token', $("meta[name='csrf-token']").attr("content"))

    $.ajax({
        url: '/dokumen/store',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
            if (res.status) {
                alertModal(res.status, 'File berhasil diupload');
                setTimeout(() => {
                        location.reload();
                    }, 1000);
            }
        },
        error: function (err) {
            console.error(err);
            alertModal('Gagal upload file');
        }
    });
});

$(document).on("click", ".delete", function () {
    id_ctr = $(this).data('id');
    $("#confirm").modal("show");
})

$("#delete-confirmed").on("click", function () {
    if(id_ctr == 0){
        alertModal(false, "Data belum dipilih");
    }else{
        $.ajax({
            url: '/dokumen/delete',
            method: 'POST',
            data: {
                '_token': $("meta[name='csrf-token']").attr("content"),
                'id': id_ctr,
            },
            success: function (response) {
                if (response.status) {
                    alertModal(true, "Berhasil menghapus data");
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    alertModal(false, response.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = '';
    
                    $.each(errors, function (key, value) {
                        errorMessage += value[0] + '';
                    });
    
                    alertModal(false, errorMessage);
                } else {
                    alertModal(false, "Terjadi kesalahan saat mengirim data");
                }
            }
        })
    }
})

$(document).on("click", "#close-alert", function () {
    if(modal){
        openModal(modal);
    }
    modal = "";
})

$(document).on("click", ".lihat-file", function(){
    let file = $(this).data('file');
    $("#pdf-viewer").attr('src', '../../storage/' + file);
    $("#identitasModal").modal('show');
})

$(".close-modal").on("click", function () {
    $("#identitasModal").modal('hide');
})