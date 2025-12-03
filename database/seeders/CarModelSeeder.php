<?php

namespace Database\Seeders;

use App\Models\CarBrand;
use App\Models\CarModel;
use Illuminate\Database\Seeder;

class CarModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $models = [
            'Toyota' => ['Camry', 'Corolla', 'RAV4', 'Prius', 'Land Cruiser', 'Highlander', '4Runner', 'Tacoma', 'Tundra', 'Avalon', 'Sienna', 'C-HR', 'Yaris'],
            'Volkswagen' => ['Golf', 'Passat', 'Tiguan', 'Polo', 'Jetta', 'Touareg', 'Atlas', 'Arteon', 'T-Cross', 'T-Roc', 'ID.4', 'ID.3', 'Tiguan Allspace'],
            'Ford' => ['Focus', 'Fiesta', 'Mondeo', 'Kuga', 'Explorer', 'Mustang', 'F-150', 'Edge', 'Escape', 'Ranger', 'Bronco', 'Transit', 'Puma'],
            'Mercedes-Benz' => ['C-Class', 'E-Class', 'S-Class', 'GLC', 'A-Class', 'GLE', 'GLS', 'CLA', 'CLS', 'G-Class', 'EQC', 'GLB', 'AMG GT'],
            'BMW' => ['3 Series', '5 Series', 'X3', 'X5', '1 Series', 'X1', 'X7', '7 Series', '4 Series', 'X6', 'iX', 'i4', 'M3', 'M5'],
            'Audi' => ['A4', 'A6', 'Q5', 'Q7', 'A3', 'Q3', 'A5', 'A7', 'Q8', 'e-tron', 'TT', 'A8', 'RS6'],
            'Nissan' => ['Altima', 'Sentra', 'Rogue', 'Pathfinder', 'Maxima', 'Murano', 'Armada', 'Frontier', 'Titan', 'Leaf', '370Z', 'GT-R', 'Versa'],
            'Hyundai' => ['Elantra', 'Sonata', 'Tucson', 'Santa Fe', 'Accent', 'Kona', 'Palisade', 'Venue', 'Ioniq', 'Nexo', 'Genesis', 'Veloster'],
            'Kia' => ['Optima', 'Rio', 'Sportage', 'Sorento', 'Cerato', 'Seltos', 'Telluride', 'Stinger', 'Soul', 'Niro', 'Carnival', 'EV6'],
            'Mazda' => ['Mazda3', 'Mazda6', 'CX-5', 'CX-9', 'MX-5', 'CX-30', 'CX-3', 'CX-50', 'MX-30'],
            'Honda' => ['Civic', 'Accord', 'CR-V', 'Pilot', 'HR-V', 'Odyssey', 'Ridgeline', 'Passport', 'Insight', 'Clarity', 'Fit'],
            'Chevrolet' => ['Malibu', 'Equinox', 'Tahoe', 'Silverado', 'Traverse', 'Camaro', 'Corvette', 'Blazer', 'Trax', 'Bolt', 'Suburban'],
            'Renault' => ['Clio', 'Megane', 'Captur', 'Kadjar', 'Koleos', 'Duster', 'Arkana', 'Talisman', 'Scenic', 'Espace'],
            'Peugeot' => ['208', '308', '3008', '5008', '2008', '508', 'Partner', 'Expert', 'Traveller', 'Rifter'],
            'Skoda' => ['Octavia', 'Superb', 'Kodiaq', 'Karoq', 'Kamiq', 'Fabia', 'Scala', 'Enyaq', 'Rapid'],
            'Volvo' => ['XC60', 'XC90', 'XC40', 'S60', 'S90', 'V60', 'V90', 'C40', 'EX90'],
            'Lexus' => ['ES', 'RX', 'NX', 'GX', 'LX', 'IS', 'LS', 'UX', 'LC', 'RC'],
            'Subaru' => ['Outback', 'Forester', 'Crosstrek', 'Ascent', 'Impreza', 'Legacy', 'WRX', 'BRZ'],
            'Jeep' => ['Wrangler', 'Grand Cherokee', 'Cherokee', 'Compass', 'Renegade', 'Gladiator', 'Wagoneer'],
            'Land Rover' => ['Range Rover', 'Range Rover Sport', 'Discovery', 'Defender', 'Evoque', 'Velar'],
            'Porsche' => ['911', 'Cayenne', 'Macan', 'Panamera', 'Taycan', 'Boxster', 'Cayman'],
            'Tesla' => ['Model 3', 'Model S', 'Model X', 'Model Y', 'Cybertruck', 'Roadster'],
            'Mitsubishi' => ['Outlander', 'Eclipse Cross', 'ASX', 'L200', 'Pajero', 'Mirage'],
            'Suzuki' => ['Vitara', 'S-Cross', 'Swift', 'SX4', 'Jimny', 'Grand Vitara'],
            'Fiat' => ['500', 'Panda', 'Tipo', 'Doblo', 'Ducato', '500X', '500L'],
            'Opel' => ['Corsa', 'Astra', 'Crossland', 'Grandland', 'Insignia', 'Mokka', 'Combo'],
            'Citroen' => ['C3', 'C4', 'C5', 'Berlingo', 'Cactus', 'Spacetourer', 'C3 Aircross'],
            'Seat' => ['Leon', 'Ibiza', 'Ateca', 'Tarraco', 'Arona', 'Formentor', 'Cupra'],
            'Dacia' => ['Sandero', 'Duster', 'Logan', 'Spring', 'Jogger', 'Lodgy'],
            'Mini' => ['Cooper', 'Countryman', 'Clubman', 'Paceman', 'Convertible'],
            'Jaguar' => ['F-Pace', 'E-Pace', 'XF', 'XE', 'XJ', 'I-Pace', 'F-Type'],
            'Infiniti' => ['Q50', 'Q60', 'QX50', 'QX60', 'QX80', 'QX30'],
            'Acura' => ['MDX', 'RDX', 'TLX', 'ILX', 'NSX', 'Integra'],
            'Genesis' => ['G70', 'G80', 'G90', 'GV70', 'GV80', 'GV60'],
            'Alfa Romeo' => ['Giulia', 'Stelvio', 'Tonale', '4C'],
            'Chrysler' => ['300', 'Pacifica', 'Voyager'],
            'Dodge' => ['Charger', 'Challenger', 'Durango', 'Journey'],
            'Ram' => ['1500', '2500', '3500', 'ProMaster'],
            'GMC' => ['Sierra', 'Yukon', 'Acadia', 'Terrain', 'Canyon'],
            'Cadillac' => ['Escalade', 'XT5', 'XT6', 'CT5', 'CT4', 'Lyriq'],
            'Lincoln' => ['Navigator', 'Aviator', 'Corsair', 'Nautilus', 'Continental'],
        ];

        foreach ($models as $brandName => $modelNames) {
            $brand = CarBrand::where('name', $brandName)->first();
            
            if ($brand) {
                foreach ($modelNames as $modelName) {
                    CarModel::create([
                        'brand_id' => $brand->id,
                        'name' => $modelName,
                    ]);
                }
            }
        }
    }
}
