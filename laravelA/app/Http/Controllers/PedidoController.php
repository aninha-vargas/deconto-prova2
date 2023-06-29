<?php

namespace App\Http\Controllers;

use App\Services\PedidoService;

use Illuminate\Http\Request;

class PedidoController extends Controller
{
    private $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }
    public function listarPedidos()
    {
        return $this->pedidoService->listarPedidos();
    }

    public function cadastrarPedido(Request $request)
    {
        return $this->pedidoService->cadastrarPedido($request->all());
    }

    public function atualizarPedido($id, Request $request)
    {
        return $this->pedidoService->atualizarPedido($id, $request->all());
    }
}
