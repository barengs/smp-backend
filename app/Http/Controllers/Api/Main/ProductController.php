<?php

namespace App\Http\Controllers\Api\Main;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk perbankan
     *
     * Method ini digunakan untuk mengambil semua data produk perbankan dari database.
     * Produk perbankan mencakup berbagai jenis rekening seperti tabungan, giro,
     * pinjaman, dan deposito berjangka.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception Jika terjadi kesalahan saat mengambil data
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika tidak ada produk ditemukan
     */
    public function index()
    {
        try {
            // Fetch all products from the database
            $products = Product::all();

            return new ProductResource('data ditemukan', $products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch products: ' . $e->getMessage(),
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No products found',
            ], 404);
        }
    }

    /**
     * Menyimpan produk perbankan baru ke database
     *
     * Method ini digunakan untuk membuat produk perbankan baru dengan validasi input
     * yang ketat. Produk akan dibuat dengan kode produk yang unik dan validasi
     * tipe produk yang sesuai dengan standar perbankan.
     *
     * @param \Illuminate\Http\Request $request Request yang berisi data produk perbankan
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception Jika terjadi kesalahan saat menyimpan data
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_code' => 'required|string|max:20|unique:products,product_code',
                'product_name' => 'required|string|max:100',
                'product_type' => 'required|in:SAVINGS,CHECKING,LOAN,TIME_DEPOSIT',
                'interest_rate' => 'nullable|numeric|min:0|max:100',
                'admin_fee' => 'nullable|numeric|min:0',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $product = Product::create($request->all());

            return new ProductResource('Product created successfully', $product, 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create product: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan detail produk perbankan berdasarkan ID
     *
     * Method ini digunakan untuk mengambil detail produk perbankan spesifik
     * berdasarkan ID. Method ini berguna untuk melihat informasi lengkap
     * tentang produk seperti suku bunga, biaya administrasi, dan status aktif.
     *
     * @param string $id ID produk yang akan ditampilkan
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception Jika terjadi kesalahan saat mengambil data
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika produk tidak ditemukan
     */
    public function show(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            return new ProductResource('data ditemukan', $product, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch product: ' . $th->getMessage(),
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }
    }

    /**
     * Mengupdate data produk perbankan yang ada
     *
     * Method ini digunakan untuk mengubah data produk perbankan yang sudah ada
     * dengan validasi input yang ketat. Hanya field yang dikirim yang akan diupdate.
     * Method ini memastikan kode produk tetap unik saat diupdate.
     *
     * @param \Illuminate\Http\Request $request Request yang berisi data yang akan diupdate
     * @param string $id ID produk yang akan diupdate
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception Jika terjadi kesalahan saat mengupdate data
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika produk tidak ditemukan
     */
    public function update(Request $request, string $id)
    {
        try {
            $product = Product::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'product_code' => 'sometimes|required|string|max:20|unique:products,product_code,' . $id,
                'product_name' => 'sometimes|required|string|max:100',
                'product_type' => 'sometimes|required|in:SAVINGS,CHECKING,LOAN,TIME_DEPOSIT',
                'interest_rate' => 'nullable|numeric|min:0|max:100',
                'admin_fee' => 'nullable|numeric|min:0',
                'is_active' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $product->update($request->all());

            return new ProductResource('Product updated successfully', $product, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update product: ' . $th->getMessage(),
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }
    }

    /**
     * Menghapus produk perbankan dari database
     *
     * Method ini digunakan untuk menghapus produk perbankan berdasarkan ID.
     * Perlu diperhatikan bahwa penghapusan produk harus dilakukan dengan hati-hati
     * karena dapat mempengaruhi rekening nasabah yang menggunakan produk tersebut.
     *
     * @param string $id ID produk yang akan dihapus
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception Jika terjadi kesalahan saat menghapus data
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika produk tidak ditemukan
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete product: ' . $th->getMessage(),
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }
    }

    /**
     * Mengambil produk perbankan berdasarkan tipe
     *
     * Method ini digunakan untuk memfilter produk perbankan berdasarkan tipenya.
     * Tipe produk yang valid: SAVINGS (tabungan), CHECKING (giro),
     * LOAN (pinjaman), TIME_DEPOSIT (deposito berjangka).
     * Method ini berguna untuk menampilkan produk sesuai kebutuhan nasabah.
     *
     * @param string $type Tipe produk yang akan difilter
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception Jika terjadi kesalahan saat mengambil data
     */
    public function getByType(string $type)
    {
        try {
            $validator = Validator::make(['type' => $type], [
                'type' => 'required|in:SAVINGS,CHECKING,LOAN,TIME_DEPOSIT',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid product type',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $products = Product::where('product_type', $type)->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No products found for this type',
                ], 404);
            }

            return new ProductResource('data ditemukan', $products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch products: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mengambil produk perbankan yang aktif saja
     *
     * Method ini digunakan untuk menampilkan hanya produk perbankan yang aktif
     * (is_active = true). Method ini berguna untuk:
     * - Menampilkan produk yang tersedia untuk nasabah baru
     * - Filter produk yang sedang beroperasi
     * - Menghindari produk yang sudah tidak ditawarkan
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception Jika terjadi kesalahan saat mengambil data
     */
    public function getActive()
    {
        try {
            $products = Product::where('is_active', true)->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No active products found',
                ], 404);
            }

            return new ProductResource('data ditemukan', $products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch active products: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mengubah status aktif/nonaktif produk perbankan
     *
     * Method ini digunakan untuk mengubah status produk perbankan dari aktif
     * menjadi nonaktif atau sebaliknya. Method ini berguna untuk:
     * - Menonaktifkan produk yang sudah tidak ditawarkan
     * - Mengaktifkan kembali produk yang sebelumnya dinonaktifkan
     * - Mengontrol ketersediaan produk untuk nasabah
     *
     * @param string $id ID produk yang statusnya akan diubah
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception Jika terjadi kesalahan saat mengubah status
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika produk tidak ditemukan
     */
    public function toggleStatus(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->is_active = !$product->is_active;
            $product->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Product status updated successfully',
                'data' => $product,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update product status: ' . $th->getMessage(),
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }
    }
}
