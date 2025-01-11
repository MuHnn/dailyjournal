<div class="container">
    <!-- Button trigger modal -->
<button type="button" class="btn btn-secondary mb-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
    <i class="bi bi-plus-lg"></i> Tambah Gallery
</button>
    <div class="row">
        <div class="table-responsive" id="gallery_data">
        </div>
        <!-- Awal Modal Tambah-->
<div class="modal fade" id="modalTambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Gambar</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="formGroupExampleInput2" class="form-label">Gambar</label>
                        <input type="file" class="form-control" name="gambar">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <input type="submit" value="simpan" name="simpan" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
</div>

    </div>
</div>
<script>
$(document).ready(function(){
    load_data();
    function load_data(hlm){
        $.ajax({
            url : "gallery_data.php",
            method : "POST",
            data : {
					            hlm: hlm
				           },
            success : function(data){
                    $('#gallery_data').html(data);
            }
        })
    } 
    $(document).on('click', '.halaman', function(){
    var hlm = $(this).attr("id");
    load_data(hlm);
});
});
</script>

<?php
include "upload_foto.php";

// Jika tombol simpan diklik
if (isset($_POST['simpan'])) {
    $tanggal = date("Y-m-d H:i:s");
    $gambar = '';

    // Jika ada file yang dikirim
    if (!empty($_FILES['gambar']['name'])) {
        // Panggil function upload_foto untuk cek spesifikasi file
        $cek_upload = upload_foto($_FILES["gambar"]);

        // Cek status true/false
        if ($cek_upload['status']) {
            // Jika true, maka message berisi nama file gambar
            $gambar = $cek_upload['message'];
        } else {
            // Jika false, tampilkan pesan error dalam alert
            echo "<script>
                alert('" . $cek_upload['message'] . "');
                document.location='admin.php?page=gallery';
            </script>";
            die;
        }
    }

    // Cek apakah ada id yang dikirimkan dari form
    if (isset($_POST['id'])) {
        // Jika ada id, lakukan update data dengan id tersebut
        $id = $_POST['id'];

        if (empty($_FILES['gambar']['name'])) {
            // Jika tidak ganti gambar
            $gambar = $_POST['gambar_lama'];
        } else {
            // Jika ganti gambar, hapus gambar lama
            unlink("img/" . $_POST['gambar_lama']);
        }

        $stmt = $conn->prepare("UPDATE gallery 
                                SET 
                                gambar = ?,
                                tanggal = ?
                                WHERE id = ?");

        $stmt->bind_param("ssi", $gambar, $tanggal, $id);
        $simpan = $stmt->execute();
    } else {
        // Jika tidak ada id, lakukan insert data baru
        $stmt = $conn->prepare("INSERT INTO gallery (gambar, tanggal)
                                VALUES (?, ?)");

        $stmt->bind_param("ss", $gambar, $tanggal);
        $simpan = $stmt->execute();
    }

    if ($simpan) {
        echo "<script>
            alert('Simpan data sukses');
            document.location='admin.php?page=gallery';
        </script>";
    } else {
        echo "<script>
            alert('Simpan data gagal');
            document.location='admin.php?page=gallery';
        </script>";
    }

    $stmt->close();
    $conn->close();
}

// Jika tombol hapus diklik
if (isset($_POST['hapus'])) {
    $id = $_POST['id'];
    $gambar = $_POST['gambar'];

    if ($gambar != '') {
        // Hapus file gambar
        unlink("img/" . $gambar);
    }

    $stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");

    $stmt->bind_param("i", $id);
    $hapus = $stmt->execute();

    if ($hapus) {
        echo "<script>
            alert('Hapus data sukses');
            document.location='admin.php?page=gallery';
        </script>";
    } else {
        echo "<script>
            alert('Hapus data gagal');
            document.location='admin.php?page=gallery';
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
