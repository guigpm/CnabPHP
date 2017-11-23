<?php

namespace Cnab\Retorno\Cnab400;

class Detalhe extends \Cnab\Format\Linha implements \Cnab\Retorno\IDetalhe
{
    public $_codigo_banco;

    public function __construct(\Cnab\Retorno\IArquivo $arquivo)
    {
        $this->_codigo_banco = $arquivo->codigo_banco;

        $yamlLoad = new \Cnab\Format\YamlLoad($arquivo->codigo_banco, $arquivo->layoutVersao);
        $yamlLoad->load($this, 'cnab400', 'retorno/detalhe');
    }

    /**
     * Retorno se é para dar baixa no boleto.
     *
     * @return bool
     */
    public function isBaixa()
    {
        $codigo_ocorrencia = (int) $this->codigo_de_ocorrencia;

        return self::isBaixaStatic($codigo_ocorrencia, $this->_codigo_banco);
    }

    public static function isBaixaStatic($codigo, $banco = null)
    {
        if ($banco == 1) { //Banco do Brasil
            $tipo_baixa = array(6);
        } else {
            $tipo_baixa = array(9, 10, 32, 47, 59, 72);
        }

        $codigo_ocorrencia = (int) $codigo;
        if (in_array($codigo_ocorrencia, $tipo_baixa)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retorno se é uma baixa rejeitada.
     *
     * @return bool
     */
    public function isBaixaRejeitada()
    {
        $tipo_baixa = array(15);
        $codigo_ocorrencia = (int) $this->codigo_de_ocorrencia;
        if (in_array($codigo_ocorrencia, $tipo_baixa)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Identifica o tipo de detalhe, se por exemplo uma taxa de manutenção.
     *
     * @return int
     */
    public function getCodigo()
    {
        return (int) $this->codigo_de_ocorrencia;
    }

    /**
     * Retorna o valor recebido em conta.
     *
     * @return float
     */
    public function getValorRecebido()
    {
        return $this->valor_principal;
    }

    /**
     * Retorna o valor do título.
     *
     * @return float
     */
    public function getValorTitulo()
    {
        return $this->valor_do_titulo;
    }

    /**
     * Retorna o valor da tarifa.
     *
     * @return float
     */
    public function getValorTarifa()
    {
        return $this->valor_tarifa;
    }

    /**
     * Retorna o valor do Imposto sobre operações financeiras.
     *
     * @return float
     */
    public function getValorIOF()
    {
        return $this->valor_iof;
    }

    /**
     * Retorna o valor dos descontos concedido (antes da emissão).
     *
     * @return Double;
     */
    public function getValorDesconto()
    {
        return $this->valor_desconto;
    }

    /**
     * Retornna o valor dos abatimentos concedidos (depois da emissão).
     *
     * @return float
     */
    public function getValorAbatimento()
    {
        return $this->valor_abatimento;
    }

    /**
     * Retorna o valor de outros creditos.
     *
     * @return float
     */
    public function getValorOutrosCreditos()
    {
        if (\Cnab\Banco::CEF == $this->_codigo_banco) {
            return 0;
        } else {
            return $this->valor_outros_creditos;
        }
    }

    /**
     * Retorna o número do documento do boleto.
     *
     * @return string
     */
    public function getNumeroDocumento()
    {
        return trim($this->numero_do_documento);
    }

    /**
     * Retorna o nosso número do boleto (sem o digito).
     *
     * @return string
     */
    public function getNossoNumero()
    {
        return $this->nosso_numero;
    }

    /**
     * Retorna o objeto \DateTime da data de vencimento do boleto.
     *
     * @return \DateTime
     */
    public function getDataVencimento()
    {
        $data = $this->data_vencimento ? \DateTime::createFromFormat('dmy', sprintf('%06d', $this->data_vencimento)) : false;
        if ($data) {
            $data->setTime(0, 0, 0);
        }

        return $data;
    }

    /**
     * Retorna a data em que o dinheiro caiu na conta.
     *
     * @return \DateTime
     */
    public function getDataCredito()
    {
        $data = $this->data_credito ? \DateTime::createFromFormat('dmy', sprintf('%06d', $this->data_credito)) : false;
        if ($data) {
            $data->setTime(0, 0, 0);
        }

        return $data;
    }

    /**
     * Retorna o valor de juros e mora.
     */
    public function getValorMoraMulta()
    {
        if (\Cnab\Banco::CEF == $this->_codigo_banco) {
            return $this->valor_juros + $this->valor_multa;
        } else {
            return $this->valor_mora_multa;
        }
    }

    /**
     * Retorna a data da ocorrencia, o dia do pagamento.
     *
     * @return \DateTime
     */
    public function getDataOcorrencia()
    {
        $data = $this->data_de_ocorrencia ? \DateTime::createFromFormat('dmy', sprintf('%06d', $this->data_de_ocorrencia)) : false;
        if ($data) {
            $data->setTime(0, 0, 0);
        }

        return $data;
    }

    /**
     * Retorna o número da carteira do boleto.
     *
     * @return string
     */
    public function getCarteira()
    {
        return $this->carteira;
    }

    /**
     * Retorna o número da carteira do boleto.
     *
     * @return string
     */
    public function getAgencia()
    {
        return $this->agencia;
    }

    /**
     * Retorna a agencia cobradora.
     *
     * @return string
     */
    public function getAgenciaCobradora()
    {
        return $this->agencia_cobradora;
    }

    /**
     * Retorna a o dac da agencia cobradora.
     *
     * @return string
     */
    public function getAgenciaCobradoraDac()
    {
        return $this->agencia_cobradora_dac;
    }

    /**
     * Retorna o numero sequencial.
     *
     * @return Integer;
     */
    public function getNumeroSequencial()
    {
        return $this->numero_sequencial;
    }

    /**
     * Retorna o nome do código.
     *
     * @return string
     */
    public function getCodigoNome()
    {
        $codigo = $this->getCodigo();

        if (\Cnab\Banco::BRADESCO == $this->_codigo_banco) {
            if (2 == $codigo) {
                return 'Entrada Confirmada';
            } elseif (3 == $codigo) {
                return 'Entrada Rejeitada';
            } elseif (6 == $codigo) {
                return 'Liquidação normal';
            } elseif (9 == $codigo) {
                return 'Baixado Automat. via Arquivo';
            } elseif (10 == $codigo) {
                return 'Baixado conforme instruções da Agência';
            } elseif (11 == $codigo) {
                return 'Em Ser - Arquivo de Títulos pendentes';
            } elseif (12 == $codigo) {
                return 'Abatimento Concedido';
            } elseif (13 == $codigo) {
                return 'Abatimento Cancelado';
            } elseif (14 == $codigo) {
                return 'Vencimento Alterado';
            } elseif (15 == $codigo) {
                return 'Liquidação em Cartório';
            } elseif (16 == $codigo) {
                return 'Título Pago em Cheque - Vinculado';
            } elseif (17 == $codigo) {
                return 'Liquidação após baixa ou Título não registrado';
            } elseif (18 == $codigo) {
                return 'Acerto de Depositária (sem motivo)';
            } elseif (19 == $codigo) {
                return 'Confirmação Receb. Inst. de Protesto';
            } elseif (20 == $codigo) {
                return 'Confirmação Recebimento Instrução Sustação de Protesto';
            } elseif (21 == $codigo) {
                return 'Acerto do Controle do Participante';
            } elseif (22 == $codigo) {
                return 'Título Com Pagamento Cancelado';
            } elseif (23 == $codigo) {
                return 'Entrada do Título em Cartório';
            } elseif (24 == $codigo) {
                return 'Entrada rejeitada por CEP Irregular';
            } elseif (27 == $codigo) {
                return 'Baixa Rejeitada';
            } elseif (28 == $codigo) {
                return 'Débito de tarifas/custas';
            } elseif (30 == $codigo) {
                return 'Alteração de Outros Dados Rejeitados';
            } elseif (32 == $codigo) {
                return 'Instrução Rejeitada';
            } elseif (33 == $codigo) {
                return 'Confirmação Pedido Alteração Outros Dados';
            } elseif (34 == $codigo) {
                return 'Retirado de Cartório e Manutenção Carteira';
            } elseif (35 == $codigo) {
                return 'Desagendamento do débito automático';
            } elseif (40 == $codigo) {
                return 'Estorno de pagamento';
            } elseif (55 == $codigo) {
                return 'Sustado judicial';
            } elseif (68 == $codigo) {
                return 'Acerto dos dados do rateio de Crédito';
            } elseif (69 == $codigo) {
                return 'Cancelamento dos dados do rateio';
            }
        } elseif (\Cnab\Banco::CEF == $this->_codigo_banco) {
            if (1 == $codigo) {
                return 'Entrada Confirmada';
            } elseif (2 == $codigo) {
                return 'Baixa Confirmada';
            } elseif (3 == $codigo) {
                return 'Abatimento Concedido';
            } elseif (4 == $codigo) {
                return 'Abatimento Cancelado';
            } elseif (5 == $codigo) {
                return 'Vencimento Alterado';
            } elseif (6 == $codigo) {
                return 'Uso da Empresa Alterado';
            } elseif (7 == $codigo) {
                return 'Prazo de Protesto Alterado';
            } elseif (8 == $codigo) {
                return 'Prazo de Devolução Alterado';
            } elseif (9 == $codigo) {
                return 'Alteração Confirmada';
            } elseif (10 == $codigo) {
                return 'Alteração com Reemissão de Bloqueto Confirmada';
            } elseif (11 == $codigo) {
                return 'Alteração da Opção de Protesto para Devolução';
            } elseif (12 == $codigo) {
                return 'Alteração da Opção de Devolução para protesto';
            } elseif (20 == $codigo) {
                return 'Em Ser';
            } elseif (21 == $codigo) {
                return 'Liquidação';
            } elseif (22 == $codigo) {
                return 'Liquidação em Cartório';
            } elseif (23 == $codigo) {
                return 'Baixa por Devolução';
            } elseif (24 == $codigo) {
                return 'Baixa por Franco Pagamento';
            } elseif (25 == $codigo) {
                return 'Baixa por Protesto';
            } elseif (26 == $codigo) {
                return 'Título enviado para Cartório';
            } elseif (27 == $codigo) {
                return 'Sustação de Protesto';
            } elseif (28 == $codigo) {
                return 'Estorno de Protesto';
            } elseif (29 == $codigo) {
                return 'Estorno de Sustação de Protesto';
            } elseif (30 == $codigo) {
                return 'Alteração de Título';
            } elseif (31 == $codigo) {
                return 'Tarifa sobre Título Vencido';
            } elseif (32 == $codigo) {
                return 'Outras Tarifas de Alteração';
            } elseif (33 == $codigo) {
                return 'Estorno de Baixa/Liquidação';
            } elseif (34 == $codigo) {
                return 'Transferência de Carteira/Entrada';
            } elseif (35 == $codigo) {
                return 'Transferência de Carteira/Baixa';
            } elseif (99 == $codigo) {
                return 'Rejeição do Título - Cód. Rejeição informado nas POS 80 a 82';
            }
        } else {
            if ($codigo == 2) {
                return 'ENTRADA CONFIRMADA COM POSSIBILIDADE DE MENSAGEM (NOTA 20 - TABELA 10) ';
            } elseif ($codigo == 3) {
                return 'ENTRADA REJEITADA (NOTA 20 - TABELA 1)';
            } elseif ($codigo == 4) {
                return 'ALTERAÇÃO DE DADOS - NOVA ENTRADA OU ALTERAÇÃO/EXCLUSÃO DE DADOS ACATADA ';
            } elseif ($codigo == 5) {
                return 'ALTERAÇÃO DE DADOS - BAIXA';
            } elseif ($codigo == 6) {
                return 'LIQUIDAÇÃO NORMAL';
            } elseif ($codigo == 7) {
                return 'LIQUIDAÇÃO PARCIAL - COBRANÇA INTELIGENTE (B2B)';
            } elseif ($codigo == 8) {
                return 'LIQUIDAÇÃO EM CARTÓRIO ';
            } elseif ($codigo == 9) {
                return 'BAIXA SIMPLES';
            } elseif ($codigo == 10) {
                return 'BAIXA POR TER SIDO LIQUIDADO ';
            } elseif ($codigo == 11) {
                return 'EM SER (SÓ NO RETORNO MENSAL)';
            } elseif ($codigo == 12) {
                return 'ABATIMENTO CONCEDIDO ';
            } elseif ($codigo == 13) {
                return 'ABATIMENTO CANCELADO';
            } elseif ($codigo == 14) {
                return 'VENCIMENTO ALTERADO ';
            } elseif ($codigo == 15) {
                return 'BAIXAS REJEITADAS (NOTA 20 - TABELA 4)';
            } elseif ($codigo == 16) {
                return 'INSTRUÇÕES REJEITADAS (NOTA 20 - TABELA 3) ';
            } elseif ($codigo == 17) {
                return 'ALTERAÇÃO/EXCLUSÃO DE DADOS REJEITADOS (NOTA 20 - TABELA 2)';
            } elseif ($codigo == 18) {
                return 'COBRANÇA CONTRATUAL - INSTRUÇÕES/ALTERAÇÕES REJEITADAS/PENDENTES (NOTA 20 - TABELA 5) ';
            } elseif ($codigo == 19) {
                return 'CONFIRMA RECEBIMENTO DE INSTRUÇÃO DE PROTESTO';
            } elseif ($codigo == 20) {
                return 'CONFIRMA RECEBIMENTO DE INSTRUÇÃO DE SUSTAÇÃO DE PROTESTO /TARIFA';
            } elseif ($codigo == 21) {
                return 'CONFIRMA RECEBIMENTO DE INSTRUÇÃO DE NÃO PROTESTAR';
            } elseif ($codigo == 23) {
                return 'TÍTULO ENVIADO A CARTÓRIO/TARIFA';
            } elseif ($codigo == 24) {
                return 'INSTRUÇÃO DE PROTESTO REJEITADA / SUSTADA / PENDENTE (NOTA 20 - TABELA 7)';
            } elseif ($codigo == 25) {
                return 'ALEGAÇÕES DO SACADO (NOTA 20 - TABELA 6)';
            } elseif ($codigo == 26) {
                return 'TARIFA DE AVISO DE COBRANÇA';
            } elseif ($codigo == 27) {
                return 'TARIFA DE EXTRATO POSIÇÃO (B40X)';
            } elseif ($codigo == 28) {
                return 'TARIFA DE RELAÇÃO DAS LIQUIDAÇÕES';
            } elseif ($codigo == 29) {
                return 'TARIFA DE MANUTENÇÃO DE TÍTULOS VENCIDOS';
            } elseif ($codigo == 30) {
                return 'DÉBITO MENSAL DE TARIFAS (PARA ENTRADAS E BAIXAS)';
            } elseif ($codigo == 32) {
                return 'BAIXA POR TER SIDO PROTESTADO';
            } elseif ($codigo == 33) {
                return 'CUSTAS DE PROTESTO';
            } elseif ($codigo == 34) {
                return 'CUSTAS DE SUSTAÇÃO';
            } elseif ($codigo == 35) {
                return 'CUSTAS DE CARTÓRIO DISTRIBUIDOR';
            } elseif ($codigo == 36) {
                return 'CUSTAS DE EDITAL';
            } elseif ($codigo == 37) {
                return 'TARIFA DE EMISSÃO DE BOLETO/TARIFA DE ENVIO DE DUPLICATA';
            } elseif ($codigo == 38) {
                return 'TARIFA DE INSTRUÇÃO';
            } elseif ($codigo == 39) {
                return 'TARIFA DE OCORRÊNCIAS';
            } elseif ($codigo == 40) {
                return 'TARIFA MENSAL DE EMISSÃO DE BOLETO/TARIFA MENSAL DE ENVIO DE DUPLICATA';
            } elseif ($codigo == 41) {
                return 'DÉBITO MENSAL DE TARIFAS - EXTRATO DE POSIÇÃO (B4EP/B4OX)';
            } elseif ($codigo == 42) {
                return 'DÉBITO MENSAL DE TARIFAS - OUTRAS INSTRUÇÕES';
            } elseif ($codigo == 43) {
                return 'DÉBITO MENSAL DE TARIFAS - MANUTENÇÃO DE TÍTULOS VENCIDOS';
            } elseif ($codigo == 44) {
                return 'DÉBITO MENSAL DE TARIFAS - OUTRAS OCORRÊNCIAS';
            } elseif ($codigo == 45) {
                return 'DÉBITO MENSAL DE TARIFAS - PROTESTO';
            } elseif ($codigo == 46) {
                return 'DÉBITO MENSAL DE TARIFAS - SUSTAÇÃO DE PROTESTO';
            } elseif ($codigo == 47) {
                return 'BAIXA COM TRANSFERÊNCIA PARA DESCONTO';
            } elseif ($codigo == 48) {
                return 'CUSTAS DE SUSTAÇÃO JUDICIAL';
            } elseif ($codigo == 51) {
                return 'TARIFA MENSAL REF A ENTRADAS BANCOS CORRESPONDENTES NA CARTEIRA';
            } elseif ($codigo == 52) {
                return 'TARIFA MENSAL BAIXAS NA CARTEIRA';
            } elseif ($codigo == 53) {
                return 'TARIFA MENSAL BAIXAS EM BANCOS CORRESPONDENTES NA CARTEIRA';
            } elseif ($codigo == 54) {
                return 'TARIFA MENSAL DE LIQUIDAÇÕES NA CARTEIRA';
            } elseif ($codigo == 55) {
                return 'TARIFA MENSAL DE LIQUIDAÇÕES EM BANCOS CORRESPONDENTES NA CARTEIRA';
            } elseif ($codigo == 56) {
                return 'CUSTAS DE IRREGULARIDADE';
            } elseif ($codigo == 57) {
                return 'INSTRUÇÃO CANCELADA (NOTA 20 - TABELA 8)';
            } elseif ($codigo == 59) {
                return 'BAIXA POR CRÉDITO EM C/C ATRAVÉS DO SISPAG';
            } elseif ($codigo == 60) {
                return 'ENTRADA REJEITADA CARNÊ (NOTA 20 - TABELA 1)';
            } elseif ($codigo == 61) {
                return 'TARIFA EMISSÃO AVISO DE MOVIMENTAÇÃO DE TÍTULOS (2154)';
            } elseif ($codigo == 62) {
                return 'DÉBITO MENSAL DE TARIFA - AVISO DE MOVIMENTAÇÃO DE TÍTULOS (2154)';
            } elseif ($codigo == 63) {
                return 'TÍTULO SUSTADO JUDICIALMENTE';
            } elseif ($codigo == 64) {
                return 'ENTRADA CONFIRMADA COM RATEIO DE CRÉDITO';
            } elseif ($codigo == 69) {
                return 'CHEQUE DEVOLVIDO (NOTA 20 - TABELA 9)';
            } elseif ($codigo == 71) {
                return 'ENTRADA REGISTRADA, AGUARDANDO AVALIAÇÃO';
            } elseif ($codigo == 72) {
                return 'BAIXA POR CRÉDITO EM C/C ATRAVÉS DO SISPAG SEM TÍTULO CORRESPONDENTE';
            } elseif ($codigo == 73) {
                return 'CONFIRMAÇÃO DE ENTRADA NA COBRANÇA SIMPLES - ENTRADA NÃO ACEITA NA COBRANÇA CONTRATUAL';
            } elseif ($codigo == 76) {
                return 'CHEQUE COMPENSADO';
            } else {
                return 'Código Inexistente';
            }
        }
    }

    /**
     * Retorna o código de liquidação, normalmente usado para
     * saber onde o cliente efetuou o pagamento.
     *
     * @return string
     */
    public function getCodigoLiquidacao()
    {
        if ($this->existField('codigo_liquidacao')) {
            return $this->codigo_liquidacao;
        }

        return;
    }

    /**
     * Retorna a descrição do código de liquidação, normalmente usado para
     * saber onde o cliente efetuou o pagamento.
     *
     * @return string
     */
    public function getDescricaoLiquidacao()
    {
        // @TODO: Usar YAML (cnab_yaml) para criar tabela de descrição
        $codigoLiquidacao = $this->getCodigoLiquidacao();
        $tabela = array();

        if (\Cnab\Banco::ITAU == $this->_codigo_banco) {
            $tabela = array(
                'AA' => 'CAIXA ELETRÔNICO BANCO ITAÚ',
                'AC' => 'PAGAMENTO EM CARTÓRIO AUTOMATIZADO',
                'AO' => 'ACERTO ONLINE',
                'BC' => 'BANCOS CORRESPONDENTES',
                'BF' => 'ITAÚ BANKFONE',
                'BL' => 'ITAÚ BANKLINE',
                'B0' => 'OUTROS BANCOS - RECEBIMENTO OFF-LINE',
                'B1' => 'OUTROS BANCOS - PELO CÓDIGO DE BARRAS',
                'B2' => 'OUTROS BANCOS - PELA LINHA DIGITÁVEL',
                'B3' => 'OUTROS BANCOS - PELO AUTO ATENDIMENTO',
                'B4' => 'OUTROS BANCOS - RECEBIMENTO EM CASA LOTÉRICA',
                'B5' => 'OUTROS BANCOS - CORRESPONDENTE',
                'B6' => 'OUTROS BANCOS - TELEFONE',
                'B7' => 'OUTROS BANCOS - ARQUIVO ELETRÔNICO (Pagamento Efetuado por meio de troca de arquivos)',
                'CC' => 'AGÊNCIA ITAÚ - COM CHEQUE DE OUTRO BANCO ou (CHEQUE ITAÚ)*',
                'CI' => 'CORRESPONDENTE ITAÚ',
                'CK' => 'SISPAG - SISTEMA DE CONTAS A PAGAR ITAÚ',
                'CP' => 'AGÊNCIA ITAÚ - POR DÉBITO EM CONTA CORRENTE, CHEQUE ITAÚ* OU DINHEIRO',
                'DG' => 'AGÊNCIA ITAÚ - CAPTURADO EM OFF-LINE',
                'LC' => 'PAGAMENTO EM CARTÓRIO DE PROTESTO COM CHEQUE A COMPENSAR',
                'EA' => 'TERMINAL DE CAIXA',
                'Q0' => 'AGENDAMENTO - PAGAMENTO AGENDADO VIA BANKLINE OU OUTRO CANAL ELETRÔNICO E LIQUIDADO NA DATA INDICADA',
                'RA' => 'DIGITAÇÃO - REALIMENTAÇÃO AUTOMÁTICA',
                'ST' => 'PAGAMENTO VIA SELTEC**',
            );
        }

        if (array_key_exists($codigoLiquidacao, $tabela)) {
            return $tabela[$codigoLiquidacao];
        }

        return;
    }

    public function isDDA()
    {
        if ($this->existField('boleto_dda')) {
            return $this->boleto_dda ? true : false;
        }

        return false;
    }

    public function getAlegacaoPagador() {
        $tabelaTraducao = array();
        $posicoes = array();
        // @TODO: implementar funçao getAlegacaoPagador nos outros bancos
        if ($this->_codigo_banco == 341) {
            if ($this->getCodigo() == 25) {
                // * ALEGAÇÃO DO PAGADOR -- Alegações do PAGADOR (código ocorrência = 25 na Posição 109 a 110)
                $posicoes = str_split($this->erros, 4);
                $tabelaTraducao = array(
                    '1313' => 'SOLICITA A PRORROGAÇÃO DO VENCIMENTO PARA:',
                    '1321' => 'SOLICITA A DISPENSA DOS JUROS DE MORA',
                    '1339' => 'NÃO RECEBEU A MERCADORIA',
                    '1347' => 'A MERCADORIA CHEGOU ATRASADA',
                    '1354' => 'A MERCADORIA CHEGOU AVARIADA',
                    '1362' => 'A MERCADORIA CHEGOU INCOMPLETA',
                    '1370' => 'A MERCADORIA NÃO CONFERE COM O PEDIDO',
                    '1388' => 'A MERCADORIA ESTÁ À DISPOSIÇÃO',
                    '1396' => 'DEVOLVEU A MERCADORIA',
                    '1404' => 'NÃO RECEBEU A FATURA',
                    '1412' => 'A FATURA ESTÁ EM DESACORDO COM A NOTA FISCAL',
                    '1420' => 'O PEDIDO DE COMPRA FOI CANCELADO',
                    '1438' => 'A DUPLICATA FOI CANCELADA',
                    '1446' => 'QUE NADA DEVE OU COMPROU',
                    '1453' => 'QUE MANTÉM ENTENDIMENTOS COM O SACADOR',
                    '1461' => 'QUE PAGARÁ O TÍTULO EM:',
                    '1479' => 'QUE PAGOU O TÍTULO DIRETAMENTE AO BENEFICIÁRIO EM:',
                    '1487' => 'QUE PAGARÁ O TÍTULO DIRETAMENTE AO BENEFICIÁRIO EM:',
                    '1495' => 'QUE O VENCIMENTO CORRETO É:',
                    '1503' => 'QUE TEM DESCONTO OU ABATIMENTO DE:',
                    '1719' => 'PAGADOR NÃO FOI LOCALIZADO; CONFIRMAR ENDEREÇO',
                    '1727' => 'PAGADOR ESTÁ EM REGIME DE CONCORDATA',
                    '1735' => 'PAGADOR ESTÁ EM REGIME DE FALÊNCIA',
                    '1750' => 'PAGADOR SE RECUSA A PAGAR JUROS BANCÁRIOS',
                    '1768' => 'PAGADOR SE RECUSA A PAGAR COMISSÃO DE PERMANÊNCIA',
                    '1776' => 'NÃO FOI POSSÍVEL A ENTREGA DO BOLETO AO PAGADOR',
                    '1784' => 'BOLETO NÃO ENTREGUE, MUDOU-SE / DESCONHECIDO',
                    '1792' => 'BOLETO NÃO ENTREGUE, CEP ERRADO / INCOMPLETO',
                    '1800' => 'BOLETO NÃO ENTREGUE, NÚMERO NÃO EXISTE/ENDEREÇO INCOMPLETO',
                    '1818' => 'BOLETO NÃO RETIRADO PELO PAGADOR. REENVIADO PELO CORREIO PARA CARTEIRAS COM EMISSÃO PELO BANCO',
                    '1826' => 'ENDEREÇO DE E-MAIL INVÁLIDO/COBRANÇA MENSAGEM. BOLETO ENVIADO PELO CORREIO',
                    '1834' => 'BOLETO DDA, DIVIDA RECONHECIDA PELO PAGADOR',
                    '1842' => 'BOLETO DDA, DIVIDA NÃO RECONHECIDA PELO PAGADOR',
                );
            } elseif ($this->getCodigo() == 02) {
                // * REGISTRO DE MENSAGEM INFORMATIVA --  Mensagem Informativa (código de ocorrência = 02 na Posição 109 a 110)
                $posicoes = str_split($this->erros, 2);
                $tabelaTraducao = array(
                    '01' => 'CEP SEM ATENDIMENTO DE PROTESTO NO MOMENTO',
                    '02' => 'ESTADO COM DETERMINAÇÃO LEGAL QU EIMPEDE A INSCRIÇÃO DE INADIMPLENTES NOS CADASTROS DE PROTEÇÃO AO CRÉDITO NO PRAZO SOLICITADO - PRAZO SUPERIOR AO SOLICITADO',
                    '03' => 'BOLETO NÃO LIQUIDADO NO DESCONTO DE DUPLICATAS E TRANSFERIDO PARA COBRANÇA SIMPLES',
                );
            } elseif ($this->getCodigo() == 03) {
                // * REGISTROS REJEITADOS -- Entradas Rejeitadas (código da ocorrência = 03 na Posição 109 a 110)
                $posicoes = str_split($this->erros, 2);
                $tabelaTraducao = array(
                    '03' => 'AG. COBRADORA CEP SEM ATENDIMENTO DE PROTESTO NO MOMENTO',
                    '04' => 'ESTADO SIGLA DO ESTADO INVÁLIDA',
                    '05' => 'DATA VENCIMENTO PRAZO DA OPERAÇÃO MENOR QUE PRAZO MÍNIMO OU MAIOR QUE O MÁXIMO',
                    '07' => 'VALOR DO TÍTULO VALOR DO TÍTULO MAIOR QUE 10.000.000,00',
                    '08' => 'NOME DO PAGADOR NÃO INFORMADO OU DESLOCADO',
                    '09' => 'AGENCIA/CONTA AGÊNCIA ENCERRADA',
                    '10' => 'LOGRADOURO NÃO INFORMADO OU DESLOCADO',
                    '11' => 'CEP CEP NÃO NUMÉRICO OU CEP INVÁLIDO',
                    '12' => 'SACADOR / AVALISTA NOME NÃO INFORMADO OU DESLOCADO (BANCOS CORRESPONDENTES)',
                    '13' => 'ESTADO/CEP CEP INCOMPATÍVEL COM A SIGLA DO ESTADO',
                    '14' => 'NOSSO NÚMERO NOSSO NÚMERO JÁ REGISTRADO NO CADASTRO DO BANCO OU FORA DA FAIXA',
                    '15' => 'NOSSO NÚMERO NOSSO NÚMERO EM DUPLICIDADE NO MESMO MOVIMENTO',
                    '18' => 'DATA DE ENTRADA DATA DE ENTRADA INVÁLIDA PARA OPERAR COM ESTA CARTEIRA',
                    '19' => 'OCORRÊNCIA OCORRÊNCIA INVÁLIDA',
                    '21' => 'AG. COBRADORA CARTEIRA NÃO ACEITA DEPOSITÁRIA CORRESPONDENTE ESTADO DA AGÊNCIA DIFERENTE DO ESTADO DO PAGADOR AG. COBRADORA NÃO CONSTA NO CADASTRO OU ENCERRANDO',
                    '22' => 'CARTEIRA CARTEIRA NÃO PERMITIDA (NECESSÁRIO CADASTRAR FAIXA LIVRE)',
                    '26' => 'AGÊNCIA/CONTA AGÊNCIA/CONTA NÃO LIBERADA PARA OPERAR COM COBRANÇA',
                    '27' => 'CNPJ INAPTO CNPJ DO BENEFICIÁRIO INAPTO DEVOLUÇÃO DE TÍTULO EM GARANTIA',
                    '29' => 'CÓDIGO EMPRESA CATEGORIA DA CONTA INVÁLIDA',
                    '30' => 'ENTRADA BLOQUEADA ENTRADAS BLOQUEADAS, CONTA SUSPENSA EM COBRANÇA',
                    '31' => 'AGÊNCIA/CONTA CONTA NÃO TEM PERMISSÃO PARA PROTESTAR (CONTATE SEU GERENTE)',
                    '35' => 'VALOR DO IOF IOF MAIOR QUE 5%',
                    '36' => 'QTDADE DE MOEDA QUANTIDADE DE MOEDA INCOMPATÍVEL COM VALOR DO TÍTULO',
                    '37' => 'CNPJ/CPF DO PAGADOR NÃO NUMÉRICO OU IGUAL A ZEROS',
                    '42' => 'NOSSO NÚMERO NOSSO NÚMERO FORA DE FAIXA',
                    '52' => 'AG. COBRADORA EMPRESA NÃO ACEITA BANCO CORRESPONDENTE',
                    '53' => 'AG. COBRADORA EMPRESA NÃO ACEITA BANCO CORRESPONDENTE - COBRANÇA MENSAGEM',
                    '54' => 'DATA DE VENCTO BANCO CORRESPONDENTE - TÍTULO COM VENCIMENTO INFERIOR A 15 DIAS',
                    '55' => 'DEP/BCO CORRESP CEP NÃO PERTENCE À DEPOSITÁRIA INFORMADA',
                    '56' => 'DT VENCTO/BCO CORRESP VENCTO SUPERIOR A 180 DIAS DA DATA DE ENTRADA',
                    '57' => 'DATA DE VENCTO CEP SÓ DEPOSITÁRIA BCO DO BRASIL COM VENCTO INFERIOR A 8 DIAS',
                    '60' => 'ABATIMENTO VALOR DO ABATIMENTO INVÁLIDO',
                    '61' => 'JUROS DE MORA JUROS DE MORA MAIOR QUE O PERMITIDO',
                    '62' => 'DESCONTO VALOR DO DESCONTO MAIOR QUE VALOR DO TÍTULO',
                    '63' => 'DESCONTO DE ANTECIPAÇÃO VALOR DA IMPORTÂNCIA POR DIA DE DESCONTO (IDD) NÃO PERMITIDO',
                    '64' => 'DATA DE EMISSÃO DATA DE EMISSÃO DO TÍTULO INVÁLIDA',
                    '65' => 'TAXA FINANCTO TAXA INVÁLIDA (VENDOR)',
                    '66' => 'DATA DE VENCTO INVALIDA/FORA DE PRAZO DE OPERAÇÃO (MÍNIMO OU MÁXIMO)',
                    '67' => 'VALOR/QTIDADE VALOR DO TÍTULO/QUANTIDADE DE MOEDA INVÁLIDO',
                    '68' => 'CARTEIRA CARTEIRA INVÁLIDA OU NÃO CADASTRADA NO INTERCÂMBIO DA COBRANÇA',
                    '69' => 'CARTEIRA CARTEIRA INVÁLIDA PARA TÍTULOS COM RATEIO DE CRÉDITO',
                    '70' => 'AGÊNCIA/CONTA BENEFICIÁRIO NÃO CADASTRADO PARA FAZER RATEIO DE CRÉDITO',
                    '78' => 'AGÊNCIA/CONTA DUPLICIDADE DE AGÊNCIA/CONTA BENEFICIÁRIA DO RATEIO DE CRÉDITO',
                    '80' => 'AGÊNCIA/CONTA QUANTIDADE DE CONTAS BENEFICIÁRIAS DO RATEIO MAIOR DO QUE O PERMITIDO (MÁXIMO DE 30 CONTAS POR TÍTULO)',
                    '81' => 'AGÊNCIA/CONTA CONTA PARA RATEIO DE CRÉDITO INVÁLIDA / NÃO PERTENCE AO ITAÚ',
                    '82' => 'DESCONTO/ABATI-MENTO DESCONTO/ABATIMENTO NÃO PERMITIDO PARA TÍTULOS COM RATEIO DE CRÉDITO',
                    '83' => 'VALOR DO TÍTULO VALOR DO TÍTULO MENOR QUE A SOMA DOS VALORES ESTIPULADOS PARA RATEIO',
                    '84' => 'AGÊNCIA/CONTA AGÊNCIA/CONTA BENEFICIÁRIA DO RATEIO É A CENTRALIZADORA DE CRÉDITO DO BENEFICIÁRIO',
                    '85' => 'AGÊNCIA/CONTA AGÊNCIA/CONTA DO BENEFICIÁRIO É CONTRATUAL / RATEIO DE CRÉDITO NÃO PERMITIDO',
                    '86' => 'TIPO DE VALOR CÓDIGO DO TIPO DE VALOR INVÁLIDO / NÃO PREVISTO PARA TÍTULOS COM RATEIO DE CRÉDITO',
                    '87' => 'AGÊNCIA/CONTA REGISTRO TIPO 4 SEM INFORMAÇÃO DE AGÊNCIAS/CONTAS BENEFICIÁRIAS DO RATEIO',
                    '90' => 'NRO DA LINHA COBRANÇA MENSAGEM - NÚMERO DA LINHA DA MENSAGEM INVÁLIDO OU QUANTIDADE DE LINHAS EXCEDIDAS',
                    '97' => 'SEM MENSAGEM COBRANÇA MENSAGEM SEM MENSAGEM (SÓ DE CAMPOS FIXOS), PORÉM COM REGISTRO DO TIPO 7 OU 8',
                    '98' => 'FLASH INVÁLIDO REGISTRO MENSAGEM SEM FLASH CADASTRADO OU FLASH INFORMADO DIFERENTE DO CADASTRADO',
                    '99' => 'FLASH INVÁLIDO CONTA DE COBRANÇA COM FLASH CADASTRADO E SEM REGISTRO DE MENSAGEM CORRESPONDENTE',
                );
            } elseif ($this->getCodigo() == 16) {
                // * REGISTROS REJEITADOS -- Instruções rejeitadas (código da ocorrência = 16 na posição 109 a 110)
                $posicoes = str_split($this->erros, 2);
                $tabelaTraducao = array(
                    '01' => 'INSTRUÇÃO/OCORRÊNCIA NÃO EXISTENTE',
                    '03' => 'CONTA NÃO TEM PERMISSÃO PARA PROTESTAR (CONTATE SEU GERENTE)',
                    '06' => 'NOSSO NÚMERO IGUAL A ZEROS',
                    '09' => 'CNPJ/CPF DO SACADOR/AVALISTA INVÁLIDO',
                    '10' => 'VALOR DO ABATIMENTO IGUAL OU MAIOR QUE O VALOR DO TÍTULO',
                    '11' => 'SEGUNDA INSTRUÇÃO/OCORRÊNCIA NÃO EXISTENTE',
                    '14' => 'REGISTRO EM DUPLICIDADE',
                    '15' => 'CNPJ/CPF INFORMADO SEM NOME DO SACADOR/AVALISTA',
                    '19' => 'VALOR DO ABATIMENTO MAIOR QUE 90% DO VALOR DO TÍTULO',
                    '20' => 'EXISTE SUSTACAO DE PROTESTO PENDENTE PARA O TITULO',
                    '21' => 'TÍTULO NÃO REGISTRADO NO SISTEMA',
                    '22' => 'TÍTULO BAIXADO OU LIQUIDADO',
                    '23' => 'INSTRUÇÃO NÃO ACEITA',
                    '24' => 'INSTRUÇÃO INCOMPATÍVEL - EXISTE INSTRUÇÃO DE PROTESTO PARA O TÍTULO',
                    '25' => 'INSTRUÇÃO INCOMPATÍVEL - NÃO EXISTE INSTRUÇÃO DE PROTESTO PARA O TÍTULO',
                    '26' => 'INSTRUÇÃO NÃO ACEITA POR JÁ TER SIDO EMITIDA A ORDEM DE PROTESTO AO CARTÓRIO',
                    '27' => 'INSTRUÇÃO NÃO ACEITA POR NÃO TER SIDO EMITIDA A ORDEM DE PROTESTO AO CARTÓRIO',
                    '28' => 'JÁ EXISTE UMA MESMA INSTRUÇÃO CADASTRADA ANTERIORMENTE PARA O TÍTULO',
                    '29' => 'VALOR LÍQUIDO + VALOR DO ABATIMENTO DIFERENTE DO VALOR DO TÍTULO REGISTRADO',
                    '30' => 'EXISTE UMA INSTRUÇÃO DE NÃO PROTESTAR ATIVA PARA O TÍTULO',
                    '31' => 'EXISTE UMA OCORRÊNCIA DO PAGADOR QUE BLOQUEIA A INSTRUÇÃO',
                    '32' => 'DEPOSITÁRIA DO TÍTULO = 9999 OU CARTEIRA NÃO ACEITA PROTESTO',
                    '33' => 'ALTERAÇÃO DE VENCIMENTO IGUAL À REGISTRADA NO SISTEMA OU QUE TORNA O TÍTULO VENCIDO',
                    '34' => 'INSTRUÇÃO DE EMISSÃO DE AVISO DE COBRANÇA PARA TÍTULO VENCIDO ANTES DO VENCIMENTO',
                    '35' => 'SOLICITAÇÃO DE CANCELAMENTO DE INSTRUÇÃO INEXISTENTE',
                    '36' => 'TÍTULO SOFRENDO ALTERAÇÃO DE CONTROLE (AGÊNCIA/CONTA/CARTEIRA/NOSSO NÚMERO)',
                    '37' => 'INSTRUÇÃO NÃO PERMITIDA PARA A CARTEIRA',
                    '38' => 'INSTRUÇÃO NÃO PERMITIDA PARA TÍTULO COM RATEIO DE CRÉDITO',
                    '40' => 'INSTRUÇÃO INCOMPATÍVEL - NÃO EXISTE INSTRUÇÃO DE NEGATIVAÇÃO EXPRESSA PARA O TÍTULO',
                    '41' => 'INSTRUÇÃO NÃO PERMITIDA - TÍTULO COM ENTRADA EM NEGATIVAÇÃO EXPRESSA',
                    '42' => 'INSTRUÇÃO NÃO PERMITIDA - TÍTULO COM NEGATIVAÇÃO EXPRESSA CONCLUÍDA',
                    '43' => 'PRAZO INVÁLIDO PARA NEGATIVAÇÃO EXPRESSA - MÍNIMO: 02 DIAS CORRIDOS APÓS O VENCIMENTO',
                    '45' => 'INSTRUÇÃO INCOMPATÍVEL PARA O MESMO TÍTULO NESTA DATA',
                    '47' => 'INSTRUÇÃO NÃO PERMITIDA - ESPÉCIE INVÁLIDA',
                    '48' => 'DADOS DO PAGADOR INVÁLIDOS ( CPF / CNPJ / NOME )',
                    '49' => 'DADOS DO ENDEREÇO DO PAGADOR INVÁLIDOS',
                    '50' => 'DATA DE EMISSÃO DO TÍTULO INVÁLIDA',
                    '51' => 'INSTRUÇÃO NÃO PERMITIDA - TÍTULO COM NEGATIVAÇÃO EXPRESSA AGENDADA',
                );
            }
        }

        foreach ($posicoes as $posicao) {
            if (array_key_exists($posicao, $tabelaTraducao)) {
                return $tabelaTraducao[$posicao];
            }
        }
        return '';
    }
}
