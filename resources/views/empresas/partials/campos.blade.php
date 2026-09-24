{{-- Campos compartilhados entre o cadastro e a edição de empresa. $empresa é null no cadastro. --}}
@php $empresa ??= null; @endphp

<x-form.input name="nome_fantasia" label="Nome Fantasia" required maxlength="100" autocomplete="organization"
    placeholder="Empresa Exemplo" :value="$empresa?->nome_fantasia" help="Ex.: Empresa Exemplo" />

<x-form.input name="razao_social" label="Razão Social" required maxlength="100" placeholder="Empresa Exemplo LTDA"
    :value="$empresa?->razao_social" help="Ex.: Empresa Exemplo LTDA" />

<x-form.input name="cnpj" label="CNPJ" required mask="cnpj" inputmode="numeric" maxlength="18"
    placeholder="00.000.000/0000-00" :value="$empresa?->cnpj" help="Formato: 00.000.000/0000-00" />

<x-form.fieldset legend="Endereço">
    <x-form.grid>
        <x-form.input name="cep" label="CEP" required mask="cep" inputmode="numeric" maxlength="9"
            placeholder="87500-000" autocomplete="postal-code" :value="$empresa?->cep" help="Formato: 00000-000"
            wrapper-class="md:col-span-3" />

        <x-form.input name="logradouro" label="Logradouro" required maxlength="100" autocomplete="address-line1"
            placeholder="Av. Paraná" :value="$empresa?->logradouro" help="Ex.: Av. Paraná" wrapper-class="md:col-span-9" />

        <x-form.input name="numero" label="Número" required inputmode="numeric" maxlength="8" placeholder="1234"
            autocomplete="address-line2" :value="$empresa?->numero" help="Ex.: 1234" wrapper-class="md:col-span-3" />

        <x-form.input name="bairro" label="Bairro" required maxlength="50" placeholder="Centro"
            :value="$empresa?->bairro" help="Ex.: Centro" wrapper-class="md:col-span-4" />

        <x-form.input name="complemento" label="Complemento" maxlength="50" placeholder="Ap., sala, bloco..."
            :value="$empresa?->complemento" help="Opcional" wrapper-class="md:col-span-5" />

        <x-form.input name="cidade" label="Cidade" required maxlength="30" autocomplete="address-level2"
            placeholder="Umuarama" :value="$empresa?->cidade" help="Ex.: Umuarama" wrapper-class="md:col-span-8" />

        <x-form.input name="estado" label="Estado" required maxlength="2" placeholder="PR" class="uppercase"
            oninput="this.value = this.value.toUpperCase()" :value="$empresa?->estado"
            help="Digite a UF (ex.: PR)" wrapper-class="md:col-span-4" />
    </x-form.grid>
</x-form.fieldset>

<x-form.input type="email" name="email" label="E-mail" required maxlength="60" autocomplete="email"
    placeholder="contato@exemplo.com.br" :value="$empresa?->email" help="Usado para contato oficial" />

<x-form.input name="telefone" label="Telefone" mask="telefone" placeholder="(44) 99999-9999 ou (44) 3333-3333"
    :value="$empresa?->telefone" help="Ex.: (44) 99999-9999 ou (44) 3333-3333" />
