<?php

namespace App\Repositories;

use App\Models\Pedido;

class PedidoRepository
{
    public function __construct()
    {
        $this->model = Pedido::class;
    }

    public function listar()
    {
        return $this->model::all();
    }

    public function criar($dados)
    {
        return $this->model::create($dados);
    }

    public function obter($id)
    {
        return $this->model::find($id);
    }

    public function filtrar($filtros, $with)
    {
        return $this->model::where($filtros)->with($with)->get();
    }

    public function atualizar($id, $dados)
    {
        $objeto = $this->obter($id);
        $objeto->fill($dados);
        $objeto->save();
        return $objeto;
    }
}
