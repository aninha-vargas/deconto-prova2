<?php

namespace App\Http\Controllers;

use App\Services\PessoaService;

use Illuminate\Http\Request;

class PessoaController extends Controller
{
    private $pessoaService;
    public function __construct(PessoaService $pessoaService)
    {
        $this->pessoaService = $pessoaService;
    }

    public function listarPessoas()
    {
        return $this->pessoaService->listarPessoas();
    }

    public function cadastrarPessoa(Request $request)
    {
        return $this->pessoaService->cadastrar($request->all());
    }

    public function atualizarPessoa($id, Request $request)
    {
        return $this->pessoaService->atualizarPessoa($id, $request->all());
    }

    public function obterPessoa(int $id)
    {
        return $this->pessoaService->obterPessoa($id);
    }

    public function receberPessoas()
    {
        return $this->pessoaService->receberPessoas();
    }
}
