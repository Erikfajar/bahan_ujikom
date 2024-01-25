<?php

namespace App\Http\Controllers;

//LOAD MODEL
use App\Models\DtPengguna;
use App\Models\User;
//PACKAGE BAWAAN
use Illuminate\Http\Request;
use File;
use App\Imports\ImportDataPenggunaClass;
use App\Exports\DataPenggunaExportView;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;
use Excel;

//LOAD PACKAGE PDF
use PDF;

//LOAD HELPER
use Tanggal;


class DataPenggunaController extends Controller
{
    public function __construct(){
        $this->Tanggal = new Tanggal();
    }

    // Menampilkan Halaman Data Pengguna
    public function index(Request $request)
    {
        // Menampung sebuah Request
        $f1=$request->input('f1');
        $data = DtPengguna::select('*');

        // Kondisi kalo var $f1 ada isinya
        if($f1){
            $data->where('isrole','=',''.$f1.'')->get();
        }

        // Kondisi apabila gak ada isi dari var $f1
        $data = $data->get();
        return view('data_pengguna.index',['data'=>$data]);
    }

    // Menampilkan Halaman Input data Pengguna
    public function input(Request $request)
    {
        return view("data_pengguna.formInput");
    }

    // Proses create Data Pengguna
    public function create(Request $request)
    {
        // dd($request);
        // $isrole=auth()->user()->isrole;

        //DECLARE REQUEST
        // $hakakses = $request->input('isrole');
        $namerole = $request->input('namerole'); // $namarole menampung request input dari namerole
        $name = $request->input('name'); //$name menampung request input dari name
        $email = $request->input('email'); // $email menampung request dari input email
        $password = $request->input('password'); // $password menampung request dari input password
        $img = $request->file('img'); // $img menampung request dari input img

        // Membuat Kondisi 
        if ($namerole === 'administrator') {
            $isrole = 1; // apabila var $namerole isi nya administrator maka buat var $isrole dengan isi 1
        } else {
            $isrole = 2; // apabila var $namerole isi nya bukan administrator maka buat var $isrole dengan isi 2
        }
        //COSTUM REQUEST
        // $namerole = null;

        //Validasi dari input yang masuk dengan ketentuan tertentu
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            // 'isrole' => 'required|numeric',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|max:80|min:8',
            'img' => 'nullable|image|mimes:jpg,png,jpeg|max:2000',
        ]);

        // Membuat kondisi apabila dari proses validasi ada yang salah maka tampilkan message
        if ($validator->fails()) {
            $errormessage='';
            foreach ($validator->errors()->all() as $message) {
                $errormessage.='<li>'.$message.'</li>';
            }
            return back()
            ->with('failed','Harap periksa kembali inputan!. <ul>'.$errormessage.'</ul>')->withInput();
        }
 
        // Membuat kondisi apabila var $img berisi sebuah file
        if (isset($img)) {
            $imageName = ''.date('YmdHis').'-'.uniqid().'.'.$img->getClientOriginalExtension();
            $destinationPath = 'images/user/';
            //CEK FILE IN FOLDER
            if (File::exists(public_path($destinationPath.$imageName))) {
                File::delete(public_path($destinationPath.$imageName));
            }
            // UPLOAD TO THE DESTINATION PATH ($dir_path) IN PUBLIC FOLDER
            $img->move($destinationPath, $imageName);
            $post['img'] = $imageName;
        } else {
            $post['img'] = null; // Kalo var $img tidak ada isinya
        }
        

        //  Fungsi ini bertanggung jawab untuk membuat dan menyimpan data pengguna baru 
        try {
            // Menyiapkan data pengguna untuk disimpan
            // $post[] -> sebuah array yang digunakan untuk menampung data pengguna sebelum disimpan ke dalam database
            $post['name'] = $name;
            $post['email_verified_at'] = now();
            $post['password'] = Hash::make($password); // Menggunakan Hash::make() untuk mengamankan kata sandi
            $post['remember_token'] = Str::random(10); // Menghasilkan token acak untuk remember_token
            $post['isrole'] = $isrole;
            $post['namerole'] = $namerole;
            $post['email'] = $email;
			$after = DtPengguna::create($post); // Proses Create data
            $data  = DtPengguna::where('id','=',$after->id)->first();
            return redirect() // kondisi apabila berhasil maka akan di arahkan ke halaman data pengguna
            ->route('data_pengguna', ['id' => $data->id])
            ->with('success', 'Data berhasil disimpan');
		}
        // kondisi apabila ada kesalahan saat proses create data maka akan di arahkan kembali ke form input
		catch(Exception $e){
			return back()
            ->withInput()
            ->with('error','Gagal memproses!');
		}
    }
    
    // Menampilkan halaman edit data pengguna
    public function edit($id)
    {
        // GET THE DATA BASED ON ID
        $data = DtPengguna::find($id); // Mencari id yang sudah di tentukan lalu di ambil dari DB
        // CHECK IS DATA FOUND
        if (!$data) { // membuat kondisi apabila tidak ada id yang di maksud dari DB
            // DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', 'item not found!');
        }
        return view('data_pengguna.formEdit', compact('data','id'));
    }

    // Proses untuk update data
    public function update($id,Request $request)
    {
        // dd($request); -> Cek data yang masuk

        // Check apakah id yang di ambil ada di DB
        if ((int) $id < 1) { 
            // Kalo ada tidak ada maka arahkan ke halaman data pengguna
            return redirect()
                ->route('data_pengguna')
                ->with('error', 'item not found!'); // kalo id tidak ditemukan maka akan muncul message errors
        }
        // $isrole=auth()->user()->isrole;

       $namerole = $request->input('namerole'); // $namarole menampung request input dari namerole
       $name = $request->input('name'); //$name menampung request input dari name
       $email = $request->input('email'); // $email menampung request dari input email
       $password = $request->input('password'); // $password menampung request dari input password
       $img = $request->file('img'); // $img menampung request dari input img

       // Membuat Kondisi 
       if ($namerole === 'administrator') {
           $isrole = 1; // apabila var $namerole isi nya administrator maka buat var $isrole dengan isi 1
       } else {
           $isrole = 2; // apabila var $namerole isi nya bukan administrator maka buat var $isrole dengan isi 2
       }
        //COSTUM REQUEST
        // $namerole = null;


        // GET THE DATA BASED ON ID
        $data = DtPengguna::find($id);
        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', 'item not found!');
        }

        /*
        * menginisialisasi variabel $img_b dan $id_b dengan nilai dari objek $old,
        * menerapkan perlakuan khusus untuk nilai null. Jika $img tidak berisi file yang diunggah, 
        * nilai $img_b diperbarui dari objek $data, dengan perlakuan khusus untuk nilai null.
         */
        $img_b=$old->img??null;
        $id_b=$old->id??'';
        if (!$img) {
            $img_b = $data->img ?? null;
        }

        //Validasi dari input yang masuk dengan ketentuan tertentu
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            // 'isrole' => 'required|numeric',
            'email' => 'nullable|email'.$id_b,
            'password' => 'nullable|max:80|min:8',
            'img' => 'nullable|image|mimes:jpg,png,jpeg|max:2000',
        ]);

        // Mengecek apakah ada error dari validasi di atas
        if ($validator->fails()) {
            $errormessage='';

            // Mengiterasi melalui pesan kesalahan validator dan menyusunnya dalam bentuk daftar HTML
            foreach ($validator->errors()->all() as $message) {
                $errormessage.='<li>'.$message.'</li>';
            }
             // Mengembalikan pengguna ke halaman sebelumnya dengan pesan kesalahan dan input sebelumnya
            return back()
            ->with('failed','Harap periksa kembali inputan!. <ul>'.$errormessage.'</ul>')->withInput();
        }

        // Kondisi mengecek apakah ada file baru
        if (isset($img)) {
            // Jika ada file gambar baru diunggah
            $imageName = ''.date('YmdHis').'-'.uniqid().'.'.$img->getClientOriginalExtension();
            $destinationPath = 'images/user/';
            
            // Mengecek apakah file sebelumnya ada di folder
            if (File::exists(public_path($destinationPath.$img_b))) {
                File::delete(public_path($destinationPath.$img_b));
            }
            // UPLOAD TO THE DESTINATION PATH ($dir_path) IN PUBLIC FOLDER
            $img->move($destinationPath, $imageName);
            $post['img'] = $imageName;
        } else {
            $post['img'] = $img_b;
        }

        //  Fungsi ini bertanggung jawab untuk membuat dan menyimpan data pengguna baru 
        try { 
            // $post[] -> sebuah array yang digunakan untuk menampung data pengguna sebelum disimpan ke dalam database
            if($password){ // Kondisi apabila var $password ada isinya
                $post['password'] = Hash::make($password);
            }
            // $post['email_verified_at'] = now();
            $post['name'] = $name;
            $post['email'] = $email;
            $post['isrole'] = $isrole;
            $post['namerole'] = $namerole;
           
            // $post['remember_token'] = Str::random(10);
            DtPengguna::where('id', $id)->update($post); // Proses update data menggunakan id
            return redirect()
            ->route('data_pengguna.edit', ['id' => $id]) // kalo berhasil maka akan di arahkan ke halaman edit
            ->with('success', 'Data berhasil disimpan'); // dan message isinya "data berhasil disimpan"
		}
		catch(Exception $e){
			return back() // Kalo gagal akan di arahklan kehalaman edit juga
            ->withInput()
            ->with('error','Gagal memproses!'); // Tetapi akan mendapat message error
		}
    
    }

    // Proses Delete Data
    public function destroy($id)
    {
        // Check apakah id yang di ambil ada di DB
        if ((int) $id < 1) {
            // Kalo ada tidak ada maka arahkan ke halaman data pengguna
            return redirect()
                ->route('data_barang')
                ->with('error', 'item not found!');// kalo id tidak ditemukan maka akan muncul message errors
        }

        $db = DtPengguna::where('id', $id); // Mencari id yang di ambil dari DB lalu di ambil
        $cek = $db->count(); // Lalu hitung total id yang di ambil
        $data = $db->first(); // lalu ambil hanya data id itu saja yang ke ambil
        $file_b = $data->img??''; // Jika nilai dari $data->img adalah null, maka variabel $file_b akan diatur menjadi string kosong 

        // Proses delete data
        try {
            // Kondisi 
            if ($cek) {

                // Jika kondisi $cek terpenuhi
                if ($file_b) { // Jika $file_b tidak kosong, file gambar sebelumnya dihapus dari direktori 'images/user/'.
                    $destinationPath = 'images/user/';
                    if (File::exists(public_path($destinationPath.$file_b))) {
                        File::delete(public_path($destinationPath.$file_b));
                    }
                }

                // Proses Delete Data
                $db->delete();
            }
            // Kalo berhasil arahkan ke halaman data pengguna
            return redirect()
            ->route('data_pengguna')
            ->with('success', 'Data berhasil dihapus'); // Kalo berhasil akan ada message success
        }

        // Kalo proses gagal
        catch(Exception $e){
            // ERROR
			return back() // Kalo gagal maka akan di arahkann kembali ke halaman sebelumnya
            ->withInput()
            ->with('error','Gagal memproses!'); // Ditambah akan ada message error
		}
    }

    // Proses Export Excell
    public function export_excel(Request $request)
    {
        // Menampung Request input f1 yang di masukan ke dalam var $f1
        $f1=$request->input('f1');
        //QUERY untuk memanggil data dari DB melalui Model DtPengguna
        $data = DtPengguna::select('*');
        
        // Kondisi apabila var $f1 ada isinya
        if($f1){
            $data->where('isrole','=',''.$f1.'')->get(); // Apabila $f1 ada isinya maka akan di buatkan var $data
                                                         // dengan perintah "cari dari column isrole yang isinya
                                                         // adalah nilai/isi dari $f1
        }
        $data = $data->get(); // Kalo $f1 tidak ada isi, maka tampilakn semua data

        // Membuat instance baru dari kelas DataPenggunaExportView dengan menggunakan objek $data sebagai argumen
        $export = new DataPenggunaExportView($data);
        
        // Set nama file export nya
        $filename = date('YmdHis') . '_data_pengguna';
        
        // Download file excel
        return Excel::download($export, $filename . '.xlsx');
    }

    // Proses export PDF
    public function export_pdf(Request $request)
    {
        // Menampung Request input f1 yang di masukan ke dalam var $f1
        $f1=$request->input('f1');
        //QUERY untuk memanggil data dari DB melalui Model DtPengguna
        $data = DtPengguna::select('*');

          // Kondisi apabila var $f1 ada isinya
        if($f1){
            $data->where('isrole','=',''.$f1.'')->get();  // Apabila $f1 ada isinya maka akan di buatkan var $data
                                                          // dengan perintah "cari dari column isrole yang isinya
                                                          // adalah nilai/isi dari $f1
        }
        $data = $data->get(); // Kalo $f1 tidak ada isi, maka tampilakn semua data

        // Pass parameters to the export view

        // Membuat instance objek PDF dengan menggunakan tampilan 'data_pengguna.exportPdf' 
        // dan menyertakan data dari objek $data. Ini menggabungkan data ke dalam tampilan PDF.
        $pdf = PDF::loadview('data_pengguna.exportPdf', ['data'=>$data]); 
        $pdf->setPaper('a4', 'portrait'); // Menentukan ukuran kertas
        $pdf->setOption(['dpi' => 150, 'defaultFont' => 'sans-serif']);// Mengatur font
        $filename = date('YmdHis') . '_data_pengguna';  // Mengatur untuk nama PDF
        return $pdf->download($filename.'.pdf'); // Proses download PDF
    }

    public function import_excel(Request $request)
    {
        // Menampung Request input f1 yang di masukan ke dalam var $f1
        $file = $request->file('file');

        //VALIDATION FORM
        $request->validate([
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        try {
            if($file){
                // IMPORT DATA
                $import = new ImportDataPenggunaClass;
                Excel::import($import, $file);
                
                // SUCCESS
                $notimportlist="";
                if ($import->listgagal) {
                    $notimportlist.="<hr> Not Register : <br> {$import->listgagal}";
                }
                return redirect()
                ->route('data_pengguna')
                ->with('success', 'Import Data berhasil,<br>
                Size '.$file->getSize().', File extention '.$file->extension().',
                Insert '.$import->insert.' data, Update '.$import->edit.' data,
                Failed '.$import->gagal.' data, <br> '.$notimportlist.'');

            } else {
                // ERROR
                return back()
                ->withInput()
                ->with('error','Gagal memproses!');
            }
            
		}
		catch(Exception $e){
			// ERROR
			return back()
            ->withInput()
            ->with('error','Gagal memproses!');
		}

    }
   
}