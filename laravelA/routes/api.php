<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\FolhaController;
use App\Http\Controllers\PessoaController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post("/message", function (Request $request) {
//     $message = $_POST['message'];
//     $mqService = new \App\Services\RabbitMQService();
//     $mqService->publish($message);
//     return view('welcome');
// });
Route::prefix('pedido')->group(function () {
    Route::get('/', [PedidoController::class, 'listarPedidos']);
    Route::post('/', [PedidoController::class, 'cadastrarPedido']);
    Route::put('/{id}', [PedidoController::class, 'atualizarPedido']);
});

Route::prefix('pessoa')->group(function () {
    Route::get('/receber', [PessoaController::class, 'receberPessoas']);
    Route::get('/', [PessoaController::class, 'listarPessoas']);
    Route::post('/', [PessoaController::class, 'cadastrarPessoa']);
    Route::put('/{id}', [PessoaController::class, 'atualizarPessoa']);
    Route::get('/{id}', [PessoaController::class, 'obterPessoa']);
});
Route::prefix('folha')->group(function () {
    Route::get('/', [FolhaController::class, 'listarFolhas']);
    Route::get('/calcular', [FolhaController::class, 'calcularFolhas']);
    Route::post('/', [FolhaController::class, 'cadastrarFolhas']);
});
