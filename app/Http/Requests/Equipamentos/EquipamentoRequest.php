<?php

namespace App\Http\Requests\Equipamentos;

use App\Enums\CategoriaEquipamento;
use App\Models\Computador;
use App\Models\Equipamento;
use App\Models\Impressora;
use App\Models\Monitor;
use App\Models\TipoEquipamento;
use App\Rules\ChaveAcessoNfe;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Regras comuns ao cadastro e à edição de equipamento, incluindo a ficha técnica da categoria do tipo.
 * A ficha chega em um array com o nome da categoria (ex.: computador[processador]) e só a da
 * categoria do tipo escolhido é validada e salva.
 */
abstract class EquipamentoRequest extends FormRequest
{
    private const MAC = '/^([0-9A-F]{2}:){5}[0-9A-F]{2}$/';

    public function authorize(): bool
    {
        return true;
    }

    /** Id do equipamento em edição (para ignorar nas regras de unicidade), ou null no cadastro. */
    abstract protected function equipamentoId(): ?int;

    public function categoria(): ?CategoriaEquipamento
    {
        return TipoEquipamento::find($this->input('tipo_equipamento_id'))?->categoria;
    }

    protected function prepareForValidation(): void
    {
        $texto = fn ($valor) => filled($valor) ? (string) str($valor)->squish() : null;
        // Aceita os separadores da exibição ("000.123.456", "3524 0512 ..."); letras seguem para a validação.
        $semSeparadores = fn ($valor) => filled($valor) ? preg_replace('/[\s.\-\/]/', '', (string) $valor) : null;

        $chaveNf = $semSeparadores($this->input('chave_acesso_nf'));
        $notaFiscal = $semSeparadores($this->input('nota_fiscal'));

        // Só a chave informada: o número da nota vem dela.
        if (! $notaFiscal && $chaveNf && ctype_digit($chaveNf) && strlen($chaveNf) === 44) {
            $notaFiscal = ChaveAcessoNfe::numeroNota($chaveNf);
        }

        $this->merge([
            'nota_fiscal' => $notaFiscal !== null ? (ltrim($notaFiscal, '0') ?: '0') : null,
            'chave_acesso_nf' => $chaveNf,
            'tipo_equipamento_id' => $this->filled('tipo_equipamento_id') ? (int) $this->input('tipo_equipamento_id') : null,
            'fabricante' => $texto($this->input('fabricante')),
            'modelo' => $texto($this->input('modelo')),
            'identificacao' => filled($this->input('identificacao')) ? mb_strtoupper($texto($this->input('identificacao'))) : null,
            'patrimonio' => $texto($this->input('patrimonio')),
            'numero_serie' => $texto($this->input('numero_serie')),
            'descricao' => filled($this->input('descricao')) ? trim($this->input('descricao')) : null,
            'valor_compra' => self::decimal($this->input('valor_compra')),
            'status' => $this->input('status') ?: 'disponivel',
        ]);

        if (is_array($this->input('computador'))) {
            $this->merge(['computador' => array_merge($this->input('computador'), [
                'mac_ethernet' => self::mac($this->input('computador.mac_ethernet')),
                'mac_wifi' => self::mac($this->input('computador.mac_wifi')),
                'possui_wifi' => $this->boolean('computador.possui_wifi'),
            ])]);
        }

        if (is_array($this->input('monitor'))) {
            $this->merge(['monitor' => array_merge($this->input('monitor'), [
                'polegadas' => self::decimal($this->input('monitor.polegadas')),
            ])]);
        }

        if (is_array($this->input('impressora'))) {
            $this->merge(['impressora' => array_merge($this->input('impressora'), [
                'mac' => self::mac($this->input('impressora.mac')),
            ])]);
        }

        if (is_array($this->input('dispositivo_movel'))) {
            $imei = fn ($valor) => filled($valor) ? preg_replace('/\D+/', '', (string) $valor) : null;

            $this->merge(['dispositivo_movel' => array_merge($this->input('dispositivo_movel'), [
                'imei_1' => $imei($this->input('dispositivo_movel.imei_1')),
                'imei_2' => $imei($this->input('dispositivo_movel.imei_2')),
                'mac' => self::mac($this->input('dispositivo_movel.mac')),
            ])]);
        }
    }

    public function rules(): array
    {
        $id = $this->equipamentoId();
        $unico = fn (string $tabela, string $coluna) => Rule::unique($tabela, $coluna)->ignore($id, 'equipamento_id');
        $unicoEquipamento = fn (string $coluna) => Rule::unique('equipamentos', $coluna)->ignore($id)->whereNull('apagado_em');

        $regras = [
            'tipo_equipamento_id' => ['required', 'integer', 'exists:tipo_equipamentos,id'],
            'fabricante' => ['required', 'string', 'max:60'],
            'modelo' => ['required', 'string', 'max:80'],
            'identificacao' => ['nullable', 'string', 'max:50', $unicoEquipamento('identificacao')],
            'numero_serie' => ['nullable', 'string', 'max:255', $unicoEquipamento('numero_serie')],
            'patrimonio' => [
                Rule::requiredIf(fn () => (float) $this->input('valor_compra') > Equipamento::VALOR_MINIMO_PATRIMONIO),
                'nullable',
                'string',
                'max:255',
                $unicoEquipamento('patrimonio'),
            ],
            'data_compra' => ['nullable', 'date', 'before_or_equal:today'],
            'valor_compra' => ['nullable', 'numeric', 'decimal:0,2', 'between:0,9999999999.99'],
            'nota_fiscal' => ['nullable', 'digits_between:1,9'],
            'chave_acesso_nf' => [
                'nullable',
                new ChaveAcessoNfe,
                // A chave contém o número da nota (posições 26 a 34).
                function (string $atributo, $chave, \Closure $falha) {
                    $nota = $this->input('nota_fiscal');
                    if ($nota && strlen((string) $chave) === 44 && ChaveAcessoNfe::numeroNota($chave) !== $nota) {
                        $falha('A chave de acesso não corresponde à nota fiscal nº ' . $nota . '.');
                    }
                },
            ],
            'descricao' => ['nullable', 'string', 'max:65535'],
        ];

        return $regras + match ($this->categoria()) {
            CategoriaEquipamento::COMPUTADOR => [
                'computador' => ['required', 'array'],
                'computador.sistema_operacional' => ['required', 'string', 'max:60'],
                'computador.processador' => ['required', 'string', 'max:100'],
                'computador.placa_video' => ['nullable', 'string', 'max:100'],
                'computador.memoria_gb' => ['required', 'integer', 'between:1,4096'],
                'computador.memoria_tipo' => ['nullable', Rule::in(Computador::TIPOS_MEMORIA)],
                'computador.memoria_formato' => ['nullable', Rule::in(Computador::FORMATOS_MEMORIA)],
                'computador.armazenamento_gb' => ['required', 'integer', 'between:1,1000000'],
                'computador.armazenamento_tipo' => ['required', Rule::in(Computador::TIPOS_ARMAZENAMENTO)],
                'computador.mac_ethernet' => ['nullable', 'regex:' . self::MAC, $unico('computadores', 'mac_ethernet')],
                'computador.possui_wifi' => ['boolean'],
                'computador.mac_wifi' => [
                    'nullable', 'required_if:computador.possui_wifi,true', 'regex:' . self::MAC,
                    'different:computador.mac_ethernet', $unico('computadores', 'mac_wifi'),
                ],
                'computador.portas_video' => ['nullable', 'array'],
                'computador.portas_video.*' => [Rule::in(Computador::PORTAS_VIDEO)],
                'computador.outras_portas' => ['nullable', 'string', 'max:150'],
                'computador.anydesk_id' => ['nullable', 'string', 'max:20'],
            ],
            CategoriaEquipamento::MONITOR => [
                'monitor' => ['required', 'array'],
                'monitor.polegadas' => ['required', 'numeric', 'between:5,120'],
                'monitor.tipo_tela' => ['nullable', Rule::in(Monitor::TIPOS_TELA)],
                'monitor.portas_video' => ['nullable', 'array'],
                'monitor.portas_video.*' => [Rule::in(Computador::PORTAS_VIDEO)],
            ],
            CategoriaEquipamento::IMPRESSORA => [
                'impressora' => ['required', 'array'],
                'impressora.tecnologia' => ['required', Rule::in(array_keys(Impressora::TECNOLOGIAS))],
                'impressora.conexoes' => ['required', 'array', 'min:1'],
                'impressora.conexoes.*' => [Rule::in(array_keys(Impressora::CONEXOES))],
                'impressora.mac' => ['nullable', 'regex:' . self::MAC, $unico('impressoras', 'mac')],
            ],
            CategoriaEquipamento::DISPOSITIVO_MOVEL => [
                'dispositivo_movel' => ['nullable', 'array'],
                'dispositivo_movel.imei_1' => ['nullable', 'digits:15', $unico('dispositivos_moveis', 'imei_1')],
                'dispositivo_movel.imei_2' => [
                    'nullable', 'digits:15', 'different:dispositivo_movel.imei_1', $unico('dispositivos_moveis', 'imei_2'),
                ],
                'dispositivo_movel.mac' => ['nullable', 'regex:' . self::MAC, $unico('dispositivos_moveis', 'mac')],
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'tipo_equipamento_id.required' => 'Selecione um tipo de equipamento.',
            'tipo_equipamento_id.exists' => 'O tipo de equipamento informado não foi encontrado.',
            'status.required' => 'Informe o status do equipamento.',
            'status.in' => 'Status inválido.',
            'valor_compra.numeric' => 'Informe o valor apenas com números (ex.: 1.234,56).',
            'valor_compra.decimal' => 'O valor pode ter no máximo 2 casas decimais.',
            'nota_fiscal.digits_between' => 'Informe apenas o número da nota fiscal (até 9 dígitos).',
            'patrimonio.required' => 'Informe o patrimônio: obrigatório para equipamentos acima de R$ '
                . number_format(Equipamento::VALOR_MINIMO_PATRIMONIO, 2, ',', '.') . '.',
            'patrimonio.unique' => 'Já existe um equipamento com este patrimônio.',
            'numero_serie.unique' => 'Já existe um equipamento com este número de série.',
            'identificacao.unique' => 'Já existe um equipamento com esta identificação.',
            'data_compra.before_or_equal' => 'A data da compra não pode ser futura.',
            '*.mac_ethernet.regex' => 'Informe o MAC no formato AA:BB:CC:DD:EE:FF.',
            '*.mac_wifi.regex' => 'Informe o MAC no formato AA:BB:CC:DD:EE:FF.',
            '*.mac.regex' => 'Informe o MAC no formato AA:BB:CC:DD:EE:FF.',
            '*.mac_ethernet.unique' => 'Este MAC já está cadastrado em outro equipamento.',
            '*.mac_wifi.unique' => 'Este MAC já está cadastrado em outro equipamento.',
            '*.mac.unique' => 'Este MAC já está cadastrado em outro equipamento.',
            'computador.mac_wifi.required_if' => 'Informe o MAC do Wi-Fi.',
            'computador.mac_wifi.different' => 'O MAC do Wi-Fi deve ser diferente do MAC do cabo de rede.',
            'dispositivo_movel.imei_1.digits' => 'O IMEI deve ter 15 dígitos.',
            'dispositivo_movel.imei_2.digits' => 'O IMEI deve ter 15 dígitos.',
            '*.imei_1.unique' => 'Este IMEI já está cadastrado em outro equipamento.',
            '*.imei_2.unique' => 'Este IMEI já está cadastrado em outro equipamento.',
            'dispositivo_movel.imei_2.different' => 'O IMEI 2 deve ser diferente do IMEI 1.',
            'impressora.conexoes.required' => 'Selecione ao menos uma conexão.',
        ];
    }

    public function attributes(): array
    {
        return [
            'tipo_equipamento_id' => 'tipo de equipamento',
            'fabricante' => 'fabricante',
            'modelo' => 'modelo',
            'identificacao' => 'identificação interna',
            'patrimonio' => 'patrimônio',
            'numero_serie' => 'número de série',
            'data_compra' => 'data da compra',
            'valor_compra' => 'valor da compra',
            'nota_fiscal' => 'nota fiscal',
            'chave_acesso_nf' => 'chave de acesso da NF-e',
            'descricao' => 'observação',
            'computador.sistema_operacional' => 'sistema operacional',
            'computador.processador' => 'processador',
            'computador.placa_video' => 'placa de vídeo',
            'computador.memoria_gb' => 'memória (GB)',
            'computador.memoria_tipo' => 'tipo de memória',
            'computador.memoria_formato' => 'formato da memória',
            'computador.armazenamento_gb' => 'armazenamento (GB)',
            'computador.armazenamento_tipo' => 'tipo de armazenamento',
            'computador.mac_ethernet' => 'MAC do cabo de rede',
            'computador.mac_wifi' => 'MAC do Wi-Fi',
            'computador.portas_video' => 'portas de vídeo',
            'computador.outras_portas' => 'outras portas',
            'computador.anydesk_id' => 'ID do AnyDesk',
            'monitor.polegadas' => 'tamanho (polegadas)',
            'monitor.tipo_tela' => 'tipo de tela',
            'monitor.portas_video' => 'portas de vídeo',
            'impressora.tecnologia' => 'tecnologia',
            'impressora.conexoes' => 'conexões',
            'impressora.mac' => 'MAC',
            'dispositivo_movel.imei_1' => 'IMEI 1',
            'dispositivo_movel.imei_2' => 'IMEI 2',
            'dispositivo_movel.mac' => 'MAC',
        ];
    }

    /** Dados validados da ficha técnica da categoria do tipo (vazio para "Genérico"). */
    public function fichaTecnica(): array
    {
        $categoria = $this->categoria();

        if (! $categoria || $categoria === CategoriaEquipamento::GENERICO) {
            return [];
        }

        $ficha = $this->validated($categoria->value) ?? [];

        if ($categoria === CategoriaEquipamento::COMPUTADOR && empty($ficha['possui_wifi'])) {
            $ficha['mac_wifi'] = null;
        }

        return array_map(fn ($valor) => $valor === '' ? null : $valor, $ficha);
    }

    /** "1.234,56" → "1234.56"; "21,5" → "21.5". Mantém "1234.56". */
    private static function decimal($valor): ?string
    {
        if (! filled($valor)) {
            return null;
        }

        $valor = trim((string) $valor);

        if (str_contains($valor, ',')) {
            return str_replace(['.', ','], ['', '.'], $valor);
        }

        // "1.500" / "12.345.678": pontos como separador de milhar.
        return preg_match('/^\d{1,3}(\.\d{3})+$/', $valor) ? str_replace('.', '', $valor) : $valor;
    }

    /** "aa-bb-cc-dd-ee-ff" / "aabbccddeeff" → "AA:BB:CC:DD:EE:FF". Valores fora do padrão seguem para a validação. */
    private static function mac($valor): ?string
    {
        if (! filled($valor)) {
            return null;
        }

        $hex = preg_replace('/[^0-9A-Fa-f]/', '', (string) $valor);

        return strlen($hex) === 12 ? mb_strtoupper(implode(':', str_split($hex, 2))) : mb_strtoupper(trim((string) $valor));
    }
}
