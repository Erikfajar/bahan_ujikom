<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DtBarang;
//LOAD MODEL
use App\Models\DtPengguna;
use Illuminate\Http\Request;
use App\Models\TmKategoriBarang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{

    public function index(Request $request)
	{
        $total_admin = DtPengguna::where('namerole','administrator')->count();
        $total_operator = DtPengguna::where('namerole','operator')->count();
        $total_user = User::count();
        $total_barang = DtBarang::count();
        $total_kategoribarang = TmKategoriBarang::count();
        $total_expired = DtBarang::select('*')->whereRaw('SUBSTR(tgl_expired,1,4)<=YEAR(CURDATE())')->count();
        #GRAFIK A -------------------------------------------------------------------------------------
        //MANUAL QUERY
        //$queryA=DB::select('SELECT tm_kategoribarang.nama, COUNT(dt_barang.id) as total FROM dt_barang RIGHT JOIN tm_kategoribarang ON dt_barang.kategori_id = tm_kategoribarang.id GROUP BY tm_kategoribarang.nama');
        //ELOQUENT BUILDER QUERY
        $queryA=DtBarang::selectRaw('tm_kategoribarang.nama, COUNT(dt_barang.id) as total')
        ->rightJoin('tm_kategoribarang', 'dt_barang.kategori_id', '=', 'tm_kategoribarang.id')
        ->groupBy('tm_kategoribarang.nama')
        ->get();
        $dataGrafikA='';
        foreach($queryA as $gr){
            $nama = $gr->nama??'';
            $total = $gr->total??0;
            $dataGrafikA .= "{ name: '{$nama}', y: {$total} },";
        }
        #GRAFIK B -------------------------------------------------------------------------------------
         //MANUAL QUERY
        $queryB=DB::select("SELECT
                CASE
                    WHEN harga_jual BETWEEN 0 AND 5000 THEN '0 - 5.000'
                    WHEN harga_jual BETWEEN 5001 AND 10000 THEN '5.001 - 10.000'
                    WHEN harga_jual BETWEEN 10001 AND 50000 THEN '10.001 - 50.000'
                    ELSE '50.001+'
                END AS range_harga,
                COUNT(*) AS total
            FROM
                dt_barang
            GROUP BY
                range_harga
            ORDER BY
                MIN(harga_jual)");
        $dataGrafikB='';
        foreach($queryB as $gr){
            $nama = $gr->range_harga??'';
            $total = $gr->total??0;
            $dataGrafikB .= "{ name: '{$nama}', y: {$total} },";
        }
        #GRAFIK C -------------------------------------------------------------------------------------
        $GrafikC = "";
        for($i=1;$i<=12;$i++){
            $queryC=DtBarang::select('*')->whereRaw('MONTH(created_at)='.$i.'')->count();
            $GrafikC .= "{$queryC},";
        }
        $dataGrafikC=rtrim($GrafikC, ',');

        #GRAFIK USER PER-BULAN -------------------------------------------------------------------------------------
        $GrafikD = "";
        for($i=1;$i<=12;$i++){
            $queryD=DtPengguna::select('*')->whereRaw('MONTH(created_at)='.$i.'')->count();
            $GrafikD .= "{$queryD},";
        }
        $dataGrafikD=rtrim($GrafikD, ',');
        
        #GRAFIK USER PER-ROLE -------------------------------------------------------------------------------------
        $queryE = DtPengguna::selectRaw('namerole, COUNT(id) as total')
        ->groupBy('namerole')
        ->get();

    $dataGrafikE = '';
    foreach ($queryE as $grg) {
        $namerole = $grg->namerole ?? '';
        $total = $grg->total ?? 0;
        $dataGrafikE .= "{ name: '{$namerole}', y: {$total} },";
    }

        return view('dashboard.index',[
            'total_user' => $total_user,
            'total_barang' => $total_barang,
            'total_kategoribarang' => $total_kategoribarang,
            'total_expired' => $total_expired,
            'dataGrafikA' => $dataGrafikA,
            'dataGrafikB' => $dataGrafikB,
            'dataGrafikC' => $dataGrafikC,
            'dataGrafikD' => $dataGrafikD,
            'dataGrafikE' => $dataGrafikE,
            'total_admin' => $total_admin,
            'total_operator' => $total_operator,
        ]);
	}

}
