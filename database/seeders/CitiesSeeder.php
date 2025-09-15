<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get states to reference
        $rj = State::where('code', 'RJ')->first();

        
        $cities = [
            // Rio de Janeiro (todas as cidades)
            ['name' => 'Angra dos Reis', 'state_id' => $rj->id],
            ['name' => 'Aperibé', 'state_id' => $rj->id],
            ['name' => 'Araruama', 'state_id' => $rj->id],
            ['name' => 'Areal', 'state_id' => $rj->id],
            ['name' => 'Armação dos Búzios', 'state_id' => $rj->id],
            ['name' => 'Arraial do Cabo', 'state_id' => $rj->id],
            ['name' => 'Barra do Piraí', 'state_id' => $rj->id],
            ['name' => 'Barra Mansa', 'state_id' => $rj->id],
            ['name' => 'Belford Roxo', 'state_id' => $rj->id],
            ['name' => 'Bom Jardim', 'state_id' => $rj->id],
            ['name' => 'Bom Jesus do Itabapoana', 'state_id' => $rj->id],
            ['name' => 'Cabo Frio', 'state_id' => $rj->id],
            ['name' => 'Cachoeiras de Macacu', 'state_id' => $rj->id],
            ['name' => 'Cambuci', 'state_id' => $rj->id],
            ['name' => 'Campos dos Goytacazes', 'state_id' => $rj->id],
            ['name' => 'Cantagalo', 'state_id' => $rj->id],
            ['name' => 'Carapebus', 'state_id' => $rj->id],
            ['name' => 'Cardoso Moreira', 'state_id' => $rj->id],
            ['name' => 'Carmo', 'state_id' => $rj->id],
            ['name' => 'Casimiro de Abreu', 'state_id' => $rj->id],
            ['name' => 'Comendador Levy Gasparian', 'state_id' => $rj->id],
            ['name' => 'Conceição de Macabu', 'state_id' => $rj->id],
            ['name' => 'Cordeiro', 'state_id' => $rj->id],
            ['name' => 'Duas Barras', 'state_id' => $rj->id],
            ['name' => 'Duque de Caxias', 'state_id' => $rj->id],
            ['name' => 'Engenheiro Paulo de Frontin', 'state_id' => $rj->id],
            ['name' => 'Guapimirim', 'state_id' => $rj->id],
            ['name' => 'Iguaba Grande', 'state_id' => $rj->id],
            ['name' => 'Itaboraí', 'state_id' => $rj->id],
            ['name' => 'Itaguaí', 'state_id' => $rj->id],
            ['name' => 'Italva', 'state_id' => $rj->id],
            ['name' => 'Itaocara', 'state_id' => $rj->id],
            ['name' => 'Itaperuna', 'state_id' => $rj->id],
            ['name' => 'Itatiaia', 'state_id' => $rj->id],
            ['name' => 'Japeri', 'state_id' => $rj->id],
            ['name' => 'Laje do Muriaé', 'state_id' => $rj->id],
            ['name' => 'Macaé', 'state_id' => $rj->id],
            ['name' => 'Macuco', 'state_id' => $rj->id],
            ['name' => 'Magé', 'state_id' => $rj->id],
            ['name' => 'Mangaratiba', 'state_id' => $rj->id],
            ['name' => 'Maricá', 'state_id' => $rj->id],
            ['name' => 'Mendes', 'state_id' => $rj->id],
            ['name' => 'Mesquita', 'state_id' => $rj->id],
            ['name' => 'Miguel Pereira', 'state_id' => $rj->id],
            ['name' => 'Miracema', 'state_id' => $rj->id],
            ['name' => 'Natividade', 'state_id' => $rj->id],
            ['name' => 'Nilópolis', 'state_id' => $rj->id],
            ['name' => 'Niterói', 'state_id' => $rj->id],
            ['name' => 'Nova Friburgo', 'state_id' => $rj->id],
            ['name' => 'Nova Iguaçu', 'state_id' => $rj->id],
            ['name' => 'Paracambi', 'state_id' => $rj->id],
            ['name' => 'Paraíba do Sul', 'state_id' => $rj->id],
            ['name' => 'Paraty', 'state_id' => $rj->id],
            ['name' => 'Paty do Alferes', 'state_id' => $rj->id],
            ['name' => 'Petrópolis', 'state_id' => $rj->id],
            ['name' => 'Pinheiral', 'state_id' => $rj->id],
            ['name' => 'Piraí', 'state_id' => $rj->id],
            ['name' => 'Porciúncula', 'state_id' => $rj->id],
            ['name' => 'Porto Real', 'state_id' => $rj->id],
            ['name' => 'Quatis', 'state_id' => $rj->id],
            ['name' => 'Queimados', 'state_id' => $rj->id],
            ['name' => 'Quissamã', 'state_id' => $rj->id],
            ['name' => 'Resende', 'state_id' => $rj->id],
            ['name' => 'Rio Bonito', 'state_id' => $rj->id],
            ['name' => 'Rio Claro', 'state_id' => $rj->id],
            ['name' => 'Rio das Flores', 'state_id' => $rj->id],
            ['name' => 'Rio das Ostras', 'state_id' => $rj->id],
            ['name' => 'Rio de Janeiro', 'state_id' => $rj->id],
            ['name' => 'Santa Maria Madalena', 'state_id' => $rj->id],
            ['name' => 'Santo Antônio de Pádua', 'state_id' => $rj->id],
            ['name' => 'São Fidélis', 'state_id' => $rj->id],
            ['name' => 'São Francisco de Itabapoana', 'state_id' => $rj->id],
            ['name' => 'São Gonçalo', 'state_id' => $rj->id],
            ['name' => 'São João da Barra', 'state_id' => $rj->id],
            ['name' => 'São João de Meriti', 'state_id' => $rj->id],
            ['name' => 'São José de Ubá', 'state_id' => $rj->id],
            ['name' => 'São José do Vale do Rio Preto', 'state_id' => $rj->id],
            ['name' => 'São Pedro da Aldeia', 'state_id' => $rj->id],
            ['name' => 'São Sebastião do Alto', 'state_id' => $rj->id],
            ['name' => 'Sapucaia', 'state_id' => $rj->id],
            ['name' => 'Saquarema', 'state_id' => $rj->id],
            ['name' => 'Seropédica', 'state_id' => $rj->id],
            ['name' => 'Silva Jardim', 'state_id' => $rj->id],
            ['name' => 'Sumidouro', 'state_id' => $rj->id],
            ['name' => 'Tanguá', 'state_id' => $rj->id],
            ['name' => 'Teresópolis', 'state_id' => $rj->id],
            ['name' => 'Trajano de Moraes', 'state_id' => $rj->id],
            ['name' => 'Três Rios', 'state_id' => $rj->id],
            ['name' => 'Valença', 'state_id' => $rj->id],
            ['name' => 'Varre-Sai', 'state_id' => $rj->id],
            ['name' => 'Vassouras', 'state_id' => $rj->id],
            ['name' => 'Volta Redonda', 'state_id' => $rj->id],
        ];

        foreach ($cities as $city) {
            City::firstOrCreate([
                'name' => $city['name'],
                'state_id' => $city['state_id']
            ]);
        }
    }
}
