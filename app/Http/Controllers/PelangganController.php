<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Http\Requests\StorePelangganRequest;
use App\Http\Requests\UpdatePelangganRequest;
use App\Models\Mitra;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use function Laravel\Prompts\select;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Pelanggan::select('id', 'alamat');
        $data = Pelanggan::with('user')->select('id', 'user_id', 'alamat') // Tambahkan relasi user
            ->when($request->search, function ($query, $search) {
                    $query->where('id', 'like', "%$search%")
                        ->orWhere('alamat', 'like', "%$search%");
            })->latest()->paginate($per);

        $no = ($data->currentPage()-1) * $per + 1;
        foreach($data as $item){
            $item->no = $no++;
        }

        return response()->json($data);
    }

    /**
     * Store a newly created pelanggan
     */
    public function store(StorePelangganRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('photo')) {
            if ($pelanggan->user->photo) {
                Storage::disk('public')->delete($pelanggan->user->photo);
            }
            $validatedData['photo'] = $request->file('photo')->store('photo', 'public');
        }
   
        $pelanggan = Pelanggan::create($validatedData);
        $pelanggan->load('user'); // load relasi user

        return response()->json([
            'success' => true,
            'pelanggan' => [
                'id' => $pelanggan->id,
                'alamat' => $pelanggan->alamat,
                'user' => [
                    'name' => $pelanggan->user->name,
                    'email' => $pelanggan->user->email,
                    'phone' => $pelanggan->user->phone,
                    'photo' => $pelanggan->user->photo,
                ],
            ],
        ]);
    }

    /**
     * Show a specific pelanggan
     */
    public function show(Pelanggan $pelanggan)
    {
        $pelanggan->load('user');
    
        return response()->json([
            // 'pelanggan'=> ['no_hp' => $pelanggan->no_hp],
            'user' => [
                    'name' => $pelanggan->user->name,
                    'email' => $pelanggan->user->email,
                    'phone' => $pelanggan->user->phone,
                    'photo' => $pelanggan->user->photo,
                    'alamat' => $pelanggan->alamat,
                    // 'password' => $pengguna->user->password,
                ],
            ]);
    
    }

    /**
     * Update an existing pelanggan
     */
    public function update(UpdatePelangganRequest $request, Pelanggan $pelanggan)
    {
        $validatedData = $request->validated();
        
        $validatedData['id'] = $request->input('id'); 
        
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        // if ($request->filled('jenis_kendaraan')) {
        //     $validatedData['jenis_kendaraan'] = max(1, min(5, $validatedData['jenis_kendaraan']));
        // }

        if ($request->hasFile('photo')) {
            if ($pelanggan->user->photo) {
                Storage::disk('public')->delete($pelanggan->user->photo);
            }
            $validatedData['photo'] = $request->file('photo')->store('photo', 'public');
        }

        
        $pelanggan->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            // 'password' => $request->password,
            'photo' => $validatedData['photo'] ?? $pelanggan->user->photo,
            // 'photo' => $request->photo,
        ]);
        
        $pelanggan->update($validatedData);
        return response()->json([
            'success' => true,
            'pelanggan' => [
                // 'id' => $pelanggan->id,
                'alamat' => $pelanggan->alamat,
            ]
        ]);
    }
    public function get()
    {
        return response()->json([
            'success' => true,
            'data' => Pelanggan::select('id',  'alamat')->get()
        ]);
    }

    public function list()
    {
        $pelanggan = Pelanggan::with('user:id,name')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->user->name,
            ];
        });

        return response()->json([
            'pelanggan' => $pelanggan,
        ]);
    }


    public function destroy(Pelanggan $pelanggan)
{
    // Hapus foto dari storage jika user memiliki foto
    if ($pelanggan->user && $pelanggan->user->photo) {
        Storage::disk('public')->delete($pelanggan->user->photo);
    }

    // Hapus data user yang terkait
    if ($pelanggan->user) {
        $pelanggan->user->delete();
    }

    // Hapus data pelanggan
    $pelanggan->delete();

    return response()->json([
        'success' => true,
        'message' => 'Data pelanggan berhasil dihapus'
    ]);
}




 public function listMitra()
    {
        return Mitra::select('id', 'nama_laundry', 'alamat_laundry', 'rating', 'harga_per_kilo')
            ->where('status_validasi', 'diterima')
            ->orderBy('nama_laundry')
            ->get();
    }

     public function tambahOrder(Request $request)
    {
        $request->validate([
            'mitra_id' => 'required|exists:mitra,id',
            'berat' => 'required|numeric|min:1'
        ]);

        $mitra = Mitra::find($request->mitra_id);

        $total = $request->berat * $mitra->harga_per_kilo;

        $trx = Transaksi::create([
            'user_id' => auth()->id(),
            'mitra_id' => $mitra->id,
            'berat' => $request->berat,
            'total_harga' => $total,
            'status' => 'menunggu_konfirmasi'
        ]);

        return response()->json([
            'message' => 'Pesanan berhasil dibuat',
            'data' => $trx
        ], 201);
    }

    // List pesanan pelanggan
    public function orderanSaya()
    {
        return Transaksi::with('mitra')
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->get();
    }

    // Detail pesanan
    public function detail($id)
    {
        return Transaksi::with('mitra')
            ->where('user_id', auth()->id())
            ->findOrFail($id);
    }
}
