<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Car;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CarImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cars = Car::all();
        
        // Sample car image URLs (using placeholder images)
        $carImageUrls = [
'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1549924231-f129b911e442?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1582639510494-c80b5de9f148?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1525609004556-c46c7d6cf023?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1534093502438-3b5c3b5b5b5b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
        ];

        // Sample license image URLs
        $licenseImageUrls = [
            'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1563720223185-11003d516935?w=600&h=400&fit=crop',
        ];

        // Track used images to avoid duplicates
        $usedCarImages = [];
        $usedLicenseImages = [];

        foreach ($cars as $car) {
            // Skip if car already has any images (car_images or car_license)
            if ($car->getMedia('car_images')->count() > 0 || $car->getMedia('car_license')->count() > 0) {
                $this->command->info("Skipping car {$car->id} - already has images");
                continue;
            }

            try {
                // Get available car images (not used yet)
                $availableCarImages = array_diff_key($carImageUrls, array_flip($usedCarImages));
                
                if (count($availableCarImages) < 2) {
                    $this->command->warn("Not enough unique car images available for car {$car->id}");
                    continue;
                }

                // Add 2-4 random car images (but no more than available)
                $numImages = min(rand(2, 4), count($availableCarImages));
                $availableKeys = array_keys($availableCarImages);
                $selectedKeys = array_rand($availableKeys, $numImages);
                
                if (!is_array($selectedKeys)) {
                    $selectedKeys = [$selectedKeys];
                }

                foreach ($selectedKeys as $keyIndex) {
                    $imageIndex = $availableKeys[$keyIndex];
                    $imageUrl = $carImageUrls[$imageIndex];
                    
                    // Mark this image as used
                    $usedCarImages[] = $imageIndex;
                    
                    $response = Http::timeout(30)->get($imageUrl);
                    
                    if ($response->successful()) {
                        $fileName = 'car_' . $car->id . '_' . time() . '_' . $imageIndex . '.jpg';
                        $filePath = storage_path('app/public/temp/' . $fileName);
                        
                        // Ensure temp directory exists
                        if (!file_exists(dirname($filePath))) {
                            mkdir(dirname($filePath), 0755, true);
                        }
                        
                        file_put_contents($filePath, $response->body());
                        
                        $car->addMedia($filePath)
                            ->toMediaCollection('car_images');
                        
                        // Clean up temp file only if it still exists
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }

                // Get available license images (not used yet)
                $availableLicenseImages = array_diff_key($licenseImageUrls, array_flip($usedLicenseImages));
                
                if (count($availableLicenseImages) > 0) {
                    $licenseKey = array_rand($availableLicenseImages);
                    $licenseUrl = $licenseImageUrls[$licenseKey];
                    
                    // Mark this license image as used
                    $usedLicenseImages[] = $licenseKey;
                    
                    $response = Http::timeout(30)->get($licenseUrl);
                    
                    if ($response->successful()) {
                        $fileName = 'license_' . $car->id . '_' . time() . '.jpg';
                        $filePath = storage_path('app/public/temp/' . $fileName);
                        
                        // Ensure temp directory exists
                        if (!file_exists(dirname($filePath))) {
                            mkdir(dirname($filePath), 0755, true);
                        }
                        
                        file_put_contents($filePath, $response->body());
                        
                        $car->addMedia($filePath)
                            ->toMediaCollection('car_license');
                        
                        // Clean up temp file only if it still exists
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                } else {
                    $this->command->warn("No unique license images available for car {$car->id}");
                }

                $this->command->info("Added images for car: {$car->model} (ID: {$car->id})");
                
            } catch (\Exception $e) {
                $this->command->error("Failed to add images for car {$car->id}: " . $e->getMessage());
            }
        }

        $this->command->info('Car images seeding completed!');
    }
}
