<?php

namespace App\Services;

use App\Repositories\PedidoRepository;
use App\Services\PessoaService;

class PedidoService
{
    private $pedidoRepository;
    private $pessoaService;

    public function __construct(PedidoRepository $pedidoRepository, PessoaService $pessoaService)
    {
        $this->pedidoRepository = $pedidoRepository;
        $this->pessoaService = $pessoaService;
    }

    public function listarPedidos()
    {
        return $this->pedidoRepository->listar();
    }

    public function cadastrarPedido($dados)
    {
        return $this->pedidoRepository->criar($dados);
    }

    public function atualizarPedido($id, $dados)
    {
        return $this->pedidoRepository->atualizar($id, $dados);
    }
}
