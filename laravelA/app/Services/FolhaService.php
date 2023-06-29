<?php

namespace App\Services;

use Exception;
use App\Repositories\FolhaRepository;
use App\Repositories\PessoaRepository;
use App\Services\RabbitMQServiceService;

use Illuminate\Support\Facades\DB;

class FolhaService
{
    private $folhaRepository;
    private $pessoaRepository;
    private $rabbitMQService;


    public function __construct(FolhaRepository $folhaRepository, RabbitMQService $rabbitMQService, PessoaRepository $pessoaRepository)
    {
        $this->folhaRepository = $folhaRepository;
        $this->rabbitMQService = $rabbitMQService;
        $this->pessoaRepository = $pessoaRepository;
    }

    public function listarFolhas()
    {
        return $this->folhaRepository->listar();
    }

    public function cadastrarFolhas($dados)
    {
        // dd($dados);
        return $this->folhaRepository->criar($dados);
    }
    public function obterFolha($id)
    {
        return $this->Repository->obter($id);
    }

    public function calcularFolhas()
    {
        try {
            DB::beginTransaction();

            $folhas = $this->listarFolhas();
            // dd($folhas);
            $retorno = [];
            foreach ($folhas as $folha) {
                $retorno['mes'] = $folha['mes'];
                $retorno['ano'] = $folha['ano'];
                $retorno['horas'] = $folha['horas'];
                $retorno['valor'] = $folha['valor'];
                $retorno['salario'] = $folha["horas"] * $folha["valor"];
                $retorno['ir'] = $this->calcularIr($folha);
                $retorno['inss'] = $this->calcularInss($folha);
                $retorno['fgts'] = $retorno['salario'] * 0.08;
                $retorno['salarioLiquido'] = $retorno['salario'] - $retorno['ir'] - $retorno['inss'];
                $retorno['funcionario'] = $this->pessoaRepository->obter($folha['id_pessoa']);

                $fila = "laravel";
                $this->rabbitMQService->publish($retorno, $fila);
            }

            if ($retorno) {
                DB::commit();
                return array(
                    "status" => true,
                    "mensagem" => "Folha exportada com sucesso",
                    "dados" => $retorno
                );
            } else {
            }
        } catch (Exception $ex) {
            DB::rollBack();
            return array(
                'status' => false,
                // 'mensagem' => "Erro ao salvar avaliação",
                'mensagem' => $ex->getMessage(),
                'dados' => [],
            );
        }
    }

    private function calcularIr($dados)
    {
        $salario = $dados["horas"] * $dados["valor"];
        switch ($salario) {
            case ($salario <= 1903.98):
                $ir = 0;
                break;
            case ($salario >= 1903.99 && $salario <= 2826.65):
                $ir = ($salario * 0.075) - 142.80;
                break;
            case ($salario >= 2826.66 && $salario <= 3751.05): // 2826.66 até R$3751.05
                $ir = ($salario * 0.15) - 354.80;
                break;
            case ($salario >= 3751.06 && $salario <= 4664.68): //De R$ 3751.06 até R$4664.68
                $ir = ($salario * 0.225) - 636.13;
                break;
            case ($salario > 4664.68): //Acima de R$ 4664.68
                $ir = ($salario * 0.275) - 869.36;
                break;
        }
        return $ir;
    }

    public function calcularInss($dados)
    {
        // Até R$ 1693.72 8%
        // De R$ 1693.73 até R$2822.90 9%
        // De R$ 2822.91 até R$5645.80 11%
        // Acima de R$ 5645.81 R$ 621.03 (fixo)

        $salario = $dados["horas"] * $dados["valor"];
        switch ($salario) {
            case ($salario <= 1693.72):
                $inss = $salario * 0.08;
                break;
            case ($salario >= 1693.73 && $salario <= 2822.90):
                $inss = $salario * 0.09;
                break;
            case ($salario >= 2822.91 && $salario <= 5645.80):
                $inss = $salario * 0.11;
                break;
            case ($salario > 5645.81):
                $inss = 621.03;
                break;
        }
        return $inss;
    }
}
