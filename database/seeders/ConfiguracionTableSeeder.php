<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfiguracionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Configuracion::create([
            "nombre_sistema" => "UNIPOL S.A.",
            "alias" => "UNIPOL",
            "logo" => "logo.png",
            "envio_email" => '{
                                "host": "smtp.hostinger.com",
                                "correo": "mensaje@emsytsrl.com",
                                "driver": "smtp",
                                "nombre": "unibienes",
                                "puerto": "587",
                                "password": "8Z@d>&kj^y",
                                "encriptado": "tls"
                            }',
        ]);
    }
}
