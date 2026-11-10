<?php
/**
 * DỮ LIỆU MẪU MỸ PHẨM CHÍNH HÃNG SHOPEE MALL - ALICE COSMETIC
 * Tất cả ảnh đã được kiểm tra HTTP 200 hoạt động 100%
 */

function getInitialCategories() {
    return [
        [
            'id' => 'tay-trang',
            'name' => 'Làm Sạch & Tẩy Trang',
            'slug' => 'tay-trang',
            'icon' => 'fa-soap',
            'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=500&q=80',
            'count' => 12,
            'description' => 'Nước tẩy trang, sữa rửa mặt dịu lành chuẩn y khoa da liễu.'
        ],
        [
            'id' => 'serum-tinh-chat',
            'name' => 'Tinh Chất & Serum',
            'slug' => 'serum-tinh-chat',
            'icon' => 'fa-droplet',
            'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=500&q=80',
            'count' => 18,
            'description' => 'Serum đặc trị mụn thâm, phục hồi B5, Niacinamide và Vitamin C căng bóng.'
        ],
        [
            'id' => 'chong-nang',
            'name' => 'Kem Chống Nắng',
            'slug' => 'chong-nang',
            'icon' => 'fa-sun',
            'image' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=500&q=80',
            'count' => 10,
            'description' => 'Chống nắng phổ rộng SPF50+ nâng tone tự nhiên, kiềm dầu kháng nước.'
        ],
        [
            'id' => 'kem-duong',
            'name' => 'Dưỡng Ẩm & Phục Hồi',
            'slug' => 'kem-duong',
            'icon' => 'fa-spa',
            'image' => 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=500&q=80',
            'count' => 14,
            'description' => 'Kem dưỡng ẩm B5, gel cấp nước khóa ẩm chuyên sâu cho mọi loại da.'
        ],
        [
            'id' => 'makeup-son',
            'name' => 'Son Môi & Trang Điểm',
            'slug' => 'makeup-son',
            'icon' => 'fa-wand-magic-sparkles',
            'image' => 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=500&q=80',
            'count' => 20,
            'description' => 'Son kem lì MLBB, cushion mỏng mịn và mascara cong mi chuẩn Hàn.'
        ],
        [
            'id' => 'combo-routine',
            'name' => 'Set Quà & Routine',
            'slug' => 'combo-routine',
            'icon' => 'fa-gift',
            'image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=500&q=80',
            'count' => 8,
            'description' => 'Set quà tặng Pink Box đóng nơ hồng pastel kèm nến thơm và thiệp viết tay.'
        ]
    ];
}

function getInitialProducts() {
    return [
        // 1. Serum La Roche-Posay
        [
            'id' => 1,
            'name' => 'Serum La Roche-Posay Effaclar Ultra Concentré Giảm Mụn Mờ Thâm',
            'slug' => 'serum-la-roche-posay-effaclar',
            'brand' => 'La Roche-Posay (Pháp)',
            'category_id' => 'serum-tinh-chat',
            'price' => 415000,
            'original_price' => 590000,
            'discount_percent' => 30,
            'rating' => 4.9,
            'review_count' => 1420,
            'sold_count' => 3850,
            'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Serum chứa phức hợp 3 Acid LHA, Salicylic Acid, Glycolic Acid kết hợp Niacinamide làm dịu mụn sưng, mờ thâm sau mụn sau 14 ngày, thông thoáng lỗ chân lông.',
            'ingredients' => 'Aqua / Water, Glycolic Acid, Niacinamide, Salicylic Acid, LHA, Hyaluronic Acid.',
            'how_to_use' => 'Dùng 2-3 giọt buổi tối sau bước toner. Vỗ nhẹ khắp mặt.',
            'is_flash_sale' => 1,
            'flash_sale_stock' => 150,
            'flash_sale_sold' => 124,
            'skin_type' => 'oil',
            'variants' => json_encode([
                ['name' => 'Dung tích 30ml', 'price' => 415000, 'original_price' => 590000],
                ['name' => 'Dung tích 50ml (Tiết Kiệm)', 'price' => 645000, 'original_price' => 890000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 101, 'name' => 'Bông tẩy trang Ipek Thổ Nhĩ Kỳ 150 miếng', 'price' => 15000, 'original_price' => 45000],
                ['id' => 102, 'name' => 'Mặt nạ tràm trà giảm viêm mụn Mediheal', 'price' => 12000, 'original_price' => 35000]
            ], JSON_UNESCAPED_UNICODE)
        ],

        // 2. Kem Chống Nắng Anessa
        [
            'id' => 2,
            'name' => 'Kem Chống Nắng Anessa Perfect UV Sunscreen Skincare Milk SPF50+ PA++++',
            'slug' => 'kem-chong-nang-anessa-perfect-uv',
            'brand' => 'Anessa (Nhật Bản)',
            'category_id' => 'chong-nang',
            'price' => 229000,
            'original_price' => 320000,
            'discount_percent' => 28,
            'rating' => 4.95,
            'review_count' => 2180,
            'sold_count' => 6420,
            'image' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Sữa chống nắng kiềm dầu số 1 Nhật Bản 21 năm liên tiếp. Kháng nước kháng mồ hôi, nâng tone nhẹ nhàng tự nhiên không vón cục.',
            'ingredients' => 'Chiết xuất trà xanh Uji, Hoa hồng vàng, Collagen, Potentilla.',
            'how_to_use' => 'Lắc đều trước khi thoa lên mặt và cổ trước khi ra ngoài 20 phút.',
            'is_flash_sale' => 1,
            'flash_sale_stock' => 200,
            'flash_sale_sold' => 186,
            'skin_type' => 'all',
            'variants' => json_encode([
                ['name' => 'Chai 20ml (Mini tiện lợi)', 'price' => 229000, 'original_price' => 320000],
                ['name' => 'Chai 60ml (Fullsize)', 'price' => 520000, 'original_price' => 715000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 103, 'name' => 'Xịt khoáng làm dịu da khoáng tươi Pháp 50ml', 'price' => 25000, 'original_price' => 70000]
            ], JSON_UNESCAPED_UNICODE)
        ],

        // 3. Son Kem Lì Rom&nd
        [
            'id' => 3,
            'name' => 'Son Kem Lì Rom&nd Zero Velvet Tint Tone Hồng Đào & Đỏ Đất MLBB',
            'slug' => 'son-kem-li-romand-zero-velvet-tint',
            'brand' => 'Rom&nd (Hàn Quốc)',
            'category_id' => 'makeup-son',
            'price' => 159000,
            'original_price' => 220000,
            'discount_percent' => 28,
            'rating' => 4.88,
            'review_count' => 3120,
            'sold_count' => 8950,
            'image' => 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Chất son velvet mờ mịn lì như nhung, nhẹ như không thoa son. Che phủ vân môi hoàn hảo, tone màu chuẩn Hàn tôn da sáng răng cực đỉnh.',
            'ingredients' => 'Dimethicone, Dầu hạt Macadamia dưỡng ẩm môi.',
            'how_to_use' => 'Dùng cọ thoa từ lòng môi tán đều ra viền môi.',
            'is_flash_sale' => 1,
            'flash_sale_stock' => 300,
            'flash_sale_sold' => 268,
            'skin_type' => 'all',
            'variants' => json_encode([
                ['name' => 'Màu #02 Joyful (Cánh Hồng Đào Nữ Tính)', 'price' => 159000, 'original_price' => 220000],
                ['name' => 'Màu #05 Witty (Cam Cháy Vintage)', 'price' => 159000, 'original_price' => 220000],
                ['name' => 'Màu #06 Deepsoul (Đỏ Nâu Trầm Quý Phái)', 'price' => 159000, 'original_price' => 220000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 104, 'name' => 'Son Dưỡng Hồng Môi Dầu Dừa Bến Tre', 'price' => 19000, 'original_price' => 50000]
            ], JSON_UNESCAPED_UNICODE)
        ],

        // 4. Nước Tẩy Trang Bioderma Nắp Hồng
        [
            'id' => 4,
            'name' => 'Nước Tẩy Trang Bioderma Sensibio H2O Cho Da Nhạy Cảm Nắp Hồng',
            'slug' => 'nuoc-tay-trang-bioderma-sensibio-h2o',
            'brand' => 'Bioderma (Pháp)',
            'category_id' => 'tay-trang',
            'price' => 139000,
            'original_price' => 195000,
            'discount_percent' => 29,
            'rating' => 4.96,
            'review_count' => 1890,
            'sold_count' => 5230,
            'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Nước tẩy trang công nghệ hạt micellar số 1 thế giới. Làm sạch sâu 99% lớp trang điểm và hạt bụi siêu mịn PM 2.5 mà vẫn giữ màng ẩm tự nhiên, không rát da.',
            'ingredients' => 'Aqua/Water, Hạt Micellar, Chiết xuất dưa leo Cucumis Sativus làm dịu da.',
            'how_to_use' => 'Thấm đẫm bông tẩy trang, lau nhẹ khắp mặt đến khi sạch.',
            'is_flash_sale' => 1,
            'flash_sale_stock' => 120,
            'flash_sale_sold' => 98,
            'skin_type' => 'sensitive',
            'variants' => json_encode([
                ['name' => 'Chai 100ml (Tiện mang du lịch)', 'price' => 139000, 'original_price' => 195000],
                ['name' => 'Chai 500ml (Bản lớn tiết kiệm)', 'price' => 389000, 'original_price' => 520000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 101, 'name' => 'Bông tẩy trang Ipek Thổ Nhĩ Kỳ 150 miếng', 'price' => 15000, 'original_price' => 45000]
            ], JSON_UNESCAPED_UNICODE)
        ],

        // 5. Tinh Chất Skin1004 Rau Má (Thay thế sản phẩm cũ bị lỗi ảnh)
        [
            'id' => 5,
            'name' => 'Tinh Chất Phục Hồi Dịu Da Chiết Xuất 100% Rau Má Skin1004 Madagascar Centella',
            'slug' => 'tinh-chat-rau-ma-skin1004-madagascar-centella',
            'brand' => 'Skin1004 (Hàn Quốc)',
            'category_id' => 'serum-tinh-chat',
            'price' => 285000,
            'original_price' => 390000,
            'discount_percent' => 27,
            'rating' => 4.93,
            'review_count' => 2450,
            'sold_count' => 7890,
            'image' => 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Chứa 100% chiết xuất rau má vùng Madagascar nguyên chất không cồn, không hương liệu. Làm dịu da kích ứng, kháng khuẩn mụn sưng và củng cố hàng rào bảo vệ da khỏe mạnh.',
            'ingredients' => '100% Centella Asiatica Extract (Chiết xuất rau má hữu cơ Madagascar).',
            'how_to_use' => 'Nhỏ 3-4 giọt trực tiếp lên da sau bước toner, vỗ nhẹ cho thẩm thấu.',
            'is_flash_sale' => 0,
            'flash_sale_stock' => 150,
            'flash_sale_sold' => 89,
            'skin_type' => 'sensitive',
            'variants' => json_encode([
                ['name' => 'Chai 55ml', 'price' => 285000, 'original_price' => 390000],
                ['name' => 'Chai 100ml (Tiết kiệm)', 'price' => 395000, 'original_price' => 550000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 102, 'name' => 'Mặt nạ tràm trà giảm viêm mụn Mediheal', 'price' => 12000, 'original_price' => 35000]
            ], JSON_UNESCAPED_UNICODE)
        ],

        // 6. Kem Dưỡng La Roche-Posay B5+
        [
            'id' => 6,
            'name' => 'Kem Dưỡng Ẩm Phục Hồi Làm Dịu Da La Roche-Posay Cicaplast Baume B5+',
            'slug' => 'kem-duong-la-roche-posay-b5-plus',
            'brand' => 'La Roche-Posay (Pháp)',
            'category_id' => 'kem-duong',
            'price' => 299000,
            'original_price' => 420000,
            'discount_percent' => 28,
            'rating' => 4.94,
            'review_count' => 1650,
            'sold_count' => 4890,
            'image' => 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Kem dưỡng phục hồi da cải tiến với vi sinh vật học Tribioma độc quyền, 5% Panthenol và Madecassoside làm dịu tức thì nóng rát sau nặn mụn, peel hay laser.',
            'ingredients' => 'Aqua / Water, Panthenol 5%, Madecassoside, Shea Butter, Zinc Gluconate.',
            'how_to_use' => 'Thoa ngày 2 lần sáng và tối lên vùng da kích ứng hoặc toàn mặt.',
            'is_flash_sale' => 0,
            'flash_sale_stock' => 120,
            'flash_sale_sold' => 88,
            'skin_type' => 'sensitive',
            'variants' => json_encode([
                ['name' => 'Tuýp 40ml', 'price' => 299000, 'original_price' => 420000],
                ['name' => 'Tuýp 100ml', 'price' => 539000, 'original_price' => 750000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 104, 'name' => 'Son Dưỡng Hồng Môi Dầu Dừa Bến Tre', 'price' => 19000, 'original_price' => 50000]
            ], JSON_UNESCAPED_UNICODE)
        ],

        // 7. Tẩy Da Chết Hóa Học Paula's Choice 2% BHA
        [
            'id' => 7,
            'name' => 'Dung Dịch Tẩy Tế Bào Chết Paula\'s Choice 2% BHA Liquid Exfoliant Thu Nhỏ Lỗ Chân Lông',
            'slug' => 'paulas-choice-2-bha-liquid-exfoliant',
            'brand' => 'Paula\'s Choice (Mỹ)',
            'category_id' => 'serum-tinh-chat',
            'price' => 335000,
            'original_price' => 450000,
            'discount_percent' => 25,
            'rating' => 4.89,
            'review_count' => 1120,
            'sold_count' => 3200,
            'image' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'BHA bán chạy số 1 toàn cầu với 2% Salicylic Acid tan trong dầu len lỏi dọn sạch bã nhờn, loại bỏ mụn đầu đen và kích thích tái tạo biểu bì da mịn màng.',
            'ingredients' => 'Water, Methylpropanediol, Salicylic Acid 2%, Camellia Oleifera Leaf Extract.',
            'how_to_use' => 'Dùng 2-3 lần/tuần vào buổi tối sau bước làm sạch. Thoa đều và không rửa lại.',
            'is_flash_sale' => 1,
            'flash_sale_stock' => 90,
            'flash_sale_sold' => 79,
            'skin_type' => 'oil',
            'variants' => json_encode([
                ['name' => 'Chai 30ml (Trial Size)', 'price' => 335000, 'original_price' => 450000],
                ['name' => 'Chai 118ml (Full Size)', 'price' => 885000, 'original_price' => 1150000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 101, 'name' => 'Bông tẩy trang Ipek Thổ Nhĩ Kỳ 150 miếng', 'price' => 15000, 'original_price' => 45000]
            ], JSON_UNESCAPED_UNICODE)
        ],

        // 8. Set Quà Routine Pink Box Luxury
        [
            'id' => 8,
            'name' => 'Set Quà Tặng Độc Quyền: Combo Routine Skincare Phục Hồi ALICE Pink Box Luxury',
            'slug' => 'set-qua-tang-routine-skincare-alice-pink-box',
            'brand' => 'ALICE Signature Edition',
            'category_id' => 'combo-routine',
            'price' => 689000,
            'original_price' => 980000,
            'discount_percent' => 30,
            'rating' => 4.98,
            'review_count' => 640,
            'sold_count' => 1580,
            'image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Bộ quà tặng giới hạn cao cấp tại ALICE Cosmetic: Nước tẩy trang Bioderma 100ml + Serum Rau má phục hồi 55ml + Kem dưỡng B5+ 40ml. Tặng kèm Hộp nơ hồng satin + Nến thơm tinh dầu hoa hồng & thiệp viết tay yêu thương!',
            'ingredients' => 'Bộ ba sản phẩm lành tính an toàn tuyệt đối cho mọi làn da.',
            'how_to_use' => 'Thứ tự routine: Tẩy trang sạch -> Serum phục hồi -> Kem dưỡng khóa ẩm.',
            'is_flash_sale' => 1,
            'flash_sale_stock' => 50,
            'flash_sale_sold' => 42,
            'skin_type' => 'all',
            'variants' => json_encode([
                ['name' => 'Set Tiêu Chuẩn (Box Hồng + Thiệp + 3 Món)', 'price' => 689000, 'original_price' => 980000],
                ['name' => 'Set Deluxe (+ Nến Thơm Tinh Dầu Cao Cấp)', 'price' => 769000, 'original_price' => 1090000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 104, 'name' => 'Son Dưỡng Hồng Môi Dầu Dừa Bến Tre', 'price' => 19000, 'original_price' => 50000]
            ], JSON_UNESCAPED_UNICODE)
        ],

        // 9. Serum The Ordinary Niacinamide 10% + Zinc 1%
        [
            'id' => 9,
            'name' => 'Tinh Chất The Ordinary Niacinamide 10% + Zinc 1% Kiềm Dầu Mờ Thâm Sáng Da',
            'slug' => 'serum-the-ordinary-niacinamide-10-zinc-1',
            'brand' => 'The Ordinary (Canada)',
            'category_id' => 'serum-tinh-chat',
            'price' => 195000,
            'original_price' => 280000,
            'discount_percent' => 30,
            'rating' => 4.86,
            'review_count' => 5410,
            'sold_count' => 14200,
            'image' => 'https://images.unsplash.com/photo-1601049541289-9b1b7bbbfe19?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1601049541289-9b1b7bbbfe19?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Serum quốc dân kiểm soát bã nhờn, làm dịu nốt mụn viêm và giảm thiểu nguy cơ tắc nghẽn lỗ chân lông với 10% Niacinamide tinh khiết và 1% Zinc PCA.',
            'ingredients' => 'Aqua, Niacinamide 10%, Zinc PCA 1%, Tamarindus Indica Seed Gum, Pentylene Glycol.',
            'how_to_use' => 'Thoa lên toàn bộ mặt vào buổi sáng và tối trước khi dùng các loại kem nặng hơn.',
            'is_flash_sale' => 1,
            'flash_sale_stock' => 250,
            'flash_sale_sold' => 231,
            'skin_type' => 'oil',
            'variants' => json_encode([
                ['name' => 'Chai 30ml', 'price' => 195000, 'original_price' => 280000],
                ['name' => 'Chai 60ml (Bản lớn)', 'price' => 349000, 'original_price' => 460000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 101, 'name' => 'Bông tẩy trang Ipek Thổ Nhĩ Kỳ 150 miếng', 'price' => 15000, 'original_price' => 45000]
            ], JSON_UNESCAPED_UNICODE)
        ],

        // 10. Sữa Rửa Mặt CeraVe Foaming Cleanser
        [
            'id' => 10,
            'name' => 'Sữa Rửa Mặt CeraVe Foaming Facial Cleanser Cho Da Thường Đến Da Dầu',
            'slug' => 'sua-rua-mat-cerave-foaming-cleanser',
            'brand' => 'CeraVe (Mỹ)',
            'category_id' => 'tay-trang',
            'price' => 289000,
            'original_price' => 390000,
            'discount_percent' => 26,
            'rating' => 4.94,
            'review_count' => 3890,
            'sold_count' => 9800,
            'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Sữa rửa mặt tạo bọt dịu nhẹ được bác sĩ da liễu khuyên dùng. Bổ sung 3 Ceramide thiết yếu, Niacinamide và Hyaluronic Acid giúp làm sạch bã nhờn mà không phá hủy màng ẩm tự nhiên.',
            'ingredients' => 'Ceramides 1, 3, 6-II, Niacinamide, Hyaluronic Acid, Cholesterol.',
            'how_to_use' => 'Làm ướt mặt, lấy một lượng vừa đủ mát-xa tạo bọt nhẹ nhàng rồi rửa sạch.',
            'is_flash_sale' => 0,
            'flash_sale_stock' => 180,
            'flash_sale_sold' => 94,
            'skin_type' => 'oil',
            'variants' => json_encode([
                ['name' => 'Chai 236ml', 'price' => 289000, 'original_price' => 390000],
                ['name' => 'Chai 473ml (Tiết kiệm)', 'price' => 415000, 'original_price' => 550000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 101, 'name' => 'Bông tẩy trang Ipek Thổ Nhĩ Kỳ 150 miếng', 'price' => 15000, 'original_price' => 45000]
            ], JSON_UNESCAPED_UNICODE)
        ],

        // 11. Kem Chống Nắng La Roche-Posay Anthelios UVmune 400
        [
            'id' => 11,
            'name' => 'Kem Chống Nắng La Roche-Posay Anthelios UVMune 400 Oil Control Gel-Cream SPF50+',
            'slug' => 'kem-chong-nang-la-roche-posay-uvmune-400',
            'brand' => 'La Roche-Posay (Pháp)',
            'category_id' => 'chong-nang',
            'price' => 379000,
            'original_price' => 510000,
            'discount_percent' => 26,
            'rating' => 4.96,
            'review_count' => 2940,
            'sold_count' => 8400,
            'image' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Màng lọc quang phổ rộng Mexoryl 400 bảo vệ da chống lại tia UVA siêu dài. Công nghệ kiềm dầu Airlicium thấm hút bã nhờn suốt 12 giờ, không nhờn rít, không cay mắt.',
            'ingredients' => 'Mexoryl 400, Nước khoáng La Roche-Posay, Airlicium, Vitamin E.',
            'how_to_use' => 'Thoa trước khi tiếp xúc ánh nắng 20 phút. Thoa lại sau khi bơi hoặc vận động nhiều.',
            'is_flash_sale' => 1,
            'flash_sale_stock' => 160,
            'flash_sale_sold' => 148,
            'skin_type' => 'oil',
            'variants' => json_encode([
                ['name' => 'Tuýp 50ml Gel-Cream Kiềm Dầu', 'price' => 379000, 'original_price' => 510000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 103, 'name' => 'Xịt khoáng làm dịu da khoáng tươi Pháp 50ml', 'price' => 25000, 'original_price' => 70000]
            ], JSON_UNESCAPED_UNICODE)
        ],

        // 12. Kem Dưỡng Klairs Midnight Blue Calming Cream
        [
            'id' => 12,
            'name' => 'Kem Dưỡng Ẩm Phục Hồi Làm Dịu Da Nhạy Cảm Klairs Midnight Blue Calming Cream',
            'slug' => 'kem-duong-klairs-midnight-blue-calming-cream',
            'brand' => 'Klairs (Hàn Quốc)',
            'category_id' => 'kem-duong',
            'price' => 325000,
            'original_price' => 450000,
            'discount_percent' => 28,
            'rating' => 4.91,
            'review_count' => 1830,
            'sold_count' => 5120,
            'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Màu xanh tự nhiên từ tinh chất hoa cúc Guaiazulene kết hợp chiết xuất rau má Centella làm dịu ngay làn da ửng đỏ cháy nắng, da vừa lấy mụn hoặc sau laser.',
            'ingredients' => 'Guaiazulene (chiết xuất hoa cúc La Mã), Centella Asiatica Extract, Ceramide 3, Hyaluronic Acid.',
            'how_to_use' => 'Chấm kem lên các vùng da kích ứng hoặc thoa đều toàn mặt bước cuối.',
            'is_flash_sale' => 0,
            'flash_sale_stock' => 110,
            'flash_sale_sold' => 73,
            'skin_type' => 'sensitive',
            'variants' => json_encode([
                ['name' => 'Hũ 30ml', 'price' => 325000, 'original_price' => 450000],
                ['name' => 'Tuýp 60ml (Bản nâng cấp)', 'price' => 489000, 'original_price' => 640000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 102, 'name' => 'Mặt nạ tràm trà giảm viêm mụn Mediheal', 'price' => 12000, 'original_price' => 35000]
            ], JSON_UNESCAPED_UNICODE)
        ],

        // 13. Son Kem Lì 3CE Velvet Lip Tint
        [
            'id' => 13,
            'name' => 'Son Kem Lì 3CE Velvet Lip Tint Tone Đỏ Hồng Cam Tây Cực Chuẩn',
            'slug' => 'son-kem-li-3ce-velvet-lip-tint',
            'brand' => '3CE Stylenanda (Hàn Quốc)',
            'category_id' => 'makeup-son',
            'price' => 245000,
            'original_price' => 340000,
            'discount_percent' => 28,
            'rating' => 4.92,
            'review_count' => 4210,
            'sold_count' => 11500,
            'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Dòng son kem lì làm nên tên tuổi 3CE toàn cầu. Chất son xốp mịn lướt nhẹ trên môi, không làm khô môi, lên màu chuẩn đét tôn sáng da tự nhiên.',
            'ingredients' => 'Dimethicone, Cyclopentasiloxane, Polyglyceryl-2 Triisostearate, Hạt sắc tố màu khoáng.',
            'how_to_use' => 'Thoa một lượng nhỏ và tán đều bằng cọ hoặc đầu ngón tay.',
            'is_flash_sale' => 1,
            'flash_sale_stock' => 180,
            'flash_sale_sold' => 165,
            'skin_type' => 'all',
            'variants' => json_encode([
                ['name' => 'Màu #Daffodil (Đỏ Đất Nữ Tính)', 'price' => 245000, 'original_price' => 340000],
                ['name' => 'Màu #Taupe (Đỏ Nâu Trầm Sang Chảnh)', 'price' => 245000, 'original_price' => 340000],
                ['name' => 'Màu #Going Right (Cam Hồng Đất MLBB)', 'price' => 245000, 'original_price' => 340000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 104, 'name' => 'Son Dưỡng Hồng Môi Dầu Dừa Bến Tre', 'price' => 19000, 'original_price' => 50000]
            ], JSON_UNESCAPED_UNICODE)
        ],

        // 14. Phấn Nước Cushion Clio Kill Cover
        [
            'id' => 14,
            'name' => 'Phấn Nước Kiềm Dầu Che Khuyết Điểm Clio Kill Cover The New Founwear Cushion SPF50+',
            'slug' => 'phan-nuoc-clio-kill-cover-the-new-founwear',
            'brand' => 'Clio (Hàn Quốc)',
            'category_id' => 'makeup-son',
            'price' => 389000,
            'original_price' => 550000,
            'discount_percent' => 29,
            'rating' => 4.95,
            'review_count' => 1980,
            'sold_count' => 4600,
            'image' => 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Cushion che khuyết điểm đỉnh cao của Clio, che phủ hoàn toàn vết thâm mụn và lỗ chân lông với lớp nền bán lì semi-matte bền màu suốt 72 giờ, tặng kèm 1 lõi thay thế.',
            'ingredients' => 'Titanium Dioxide, Niacinamide, Adenosine, Phức hợp bột ngọc trai kiềm dầu.',
            'how_to_use' => 'Dùng bông mút ấn nhẹ lấy lượng cushion vừa đủ và dặm đều lên mặt.',
            'is_flash_sale' => 0,
            'flash_sale_stock' => 90,
            'flash_sale_sold' => 52,
            'skin_type' => 'all',
            'variants' => json_encode([
                ['name' => 'Tone #02 Lingerie (Cho Da Trắng Hồng)', 'price' => 389000, 'original_price' => 550000],
                ['name' => 'Tone #03 Linen (Cho Da Trắng Sáng Tự Nhiên)', 'price' => 389000, 'original_price' => 550000],
                ['name' => 'Tone #04 Ginger (Cho Da Trung Bình Sáng)', 'price' => 389000, 'original_price' => 550000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 101, 'name' => 'Bông tẩy trang Ipek Thổ Nhĩ Kỳ 150 miếng', 'price' => 15000, 'original_price' => 45000]
            ], JSON_UNESCAPED_UNICODE)
        ],

        // 15. Mặt Nạ Ngủ Môi Laneige Lip Sleeping Mask Berry
        [
            'id' => 15,
            'name' => 'Mặt Nạ Ngủ Môi Hương Quả Mọng Laneige Lip Sleeping Mask Berry Dưỡng Ẩm Hồng Môi',
            'slug' => 'mat-na-ngu-moi-laneige-lip-sleeping-mask',
            'brand' => 'Laneige (Hàn Quốc)',
            'category_id' => 'kem-duong',
            'price' => 85000,
            'original_price' => 120000,
            'discount_percent' => 29,
            'rating' => 4.97,
            'review_count' => 6200,
            'sold_count' => 19500,
            'image' => 'https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Mặt nạ ngủ môi chiết xuất từ 8 loại quả mọng giàu Vitamin C giúp loại bỏ tế bào da chết khô ráp trên môi suốt đêm, trả lại đôi môi căng mịn, hồng hào vào sáng hôm sau.',
            'ingredients' => 'Berry Fruit Complex giàu Vitamin C, Dầu dừa, Bơ hạt mỡ dưỡng ẩm sâu.',
            'how_to_use' => 'Thoa một lớp dày lên môi trước khi đi ngủ. Sáng hôm sau nhẹ nhàng lau sạch bằng khăn ẩm.',
            'is_flash_sale' => 1,
            'flash_sale_stock' => 350,
            'flash_sale_sold' => 328,
            'skin_type' => 'all',
            'variants' => json_encode([
                ['name' => 'Hũ Mini 3g', 'price' => 85000, 'original_price' => 120000],
                ['name' => 'Hũ Fullsize 20g', 'price' => 365000, 'original_price' => 490000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 104, 'name' => 'Son Dưỡng Hồng Môi Dầu Dừa Bến Tre', 'price' => 19000, 'original_price' => 50000]
            ], JSON_UNESCAPED_UNICODE)
        ],

        // 16. Gel Dưỡng Ẩm Cấp Nước Neutrogena Hydro Boost Water Gel
        [
            'id' => 16,
            'name' => 'Kem Dưỡng Dạng Gel Cấp Nước Neutrogena Hydro Boost Water Gel Cho Da Dầu Thiếu Nước',
            'slug' => 'kem-duong-neutrogena-hydro-boost-water-gel',
            'brand' => 'Neutrogena (Mỹ)',
            'category_id' => 'kem-duong',
            'price' => 289000,
            'original_price' => 395000,
            'discount_percent' => 27,
            'rating' => 4.9,
            'review_count' => 3210,
            'sold_count' => 8700,
            'image' => 'https://images.unsplash.com/photo-1526947425960-945c6e72858f?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1526947425960-945c6e72858f?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Chất gel thạch trong suốt tan như nước trên da, thấm nhanh trong 10 giây không để lại cảm giác dính nhờn. Chứa Hyaluronic Acid tinh khiết khóa ẩm ngậm nước suốt 72 giờ.',
            'ingredients' => 'Hyaluronic Acid, Chiết xuất Olive (Olive Extract), Glycerin.',
            'how_to_use' => 'Dùng mỗi sáng và tối sau bước serum, khóa ẩm hoàn hảo cho da dầu.',
            'is_flash_sale' => 0,
            'flash_sale_stock' => 130,
            'flash_sale_sold' => 84,
            'skin_type' => 'oil',
            'variants' => json_encode([
                ['name' => 'Hũ 50g', 'price' => 289000, 'original_price' => 395000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 101, 'name' => 'Bông tẩy trang Ipek Thổ Nhĩ Kỳ 150 miếng', 'price' => 15000, 'original_price' => 45000]
            ], JSON_UNESCAPED_UNICODE)
        ]
    ];
}

function getInitialCoupons() {
    return [
        [
            'code' => 'ALICEPINK',
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'min_order' => 399000,
            'max_discount' => 50000,
            'description' => 'Giảm ngay 50.000đ cho đơn hàng mỹ phẩm từ 399k mừng khai trương ALICE Cosmetic'
        ],
        [
            'code' => 'FREESHIP',
            'discount_type' => 'fixed',
            'discount_value' => 30000,
            'min_order' => 299000,
            'max_discount' => 30000,
            'description' => 'Mã miễn phí vận chuyển toàn quốc (tối đa 30.000đ) cho đơn từ 299k'
        ],
        [
            'code' => 'ALICE10',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'min_order' => 200000,
            'max_discount' => 40000,
            'description' => 'Giảm 10% (tối đa 40.000đ) áp dụng cho mọi khách hàng mới'
        ]
    ];
}

function getInitialReviews() {
    return [
        [
            'product_id' => 1,
            'author_name' => 'Ngọc Linh (Hà Nội)',
            'rating' => 5,
            'skin_type' => 'Da dầu mụn, nhạy cảm',
            'comment' => 'Serum La Roche-Posay dùng êm dã man! Mụn sưng tối hôm trước thoa thì sáng sau đã gom cồi, bớt đỏ rõ rệt. Đóng gói hộp hồng ALICE Cosmetic siêu cẩn thận, lại được tặng kèm mẫu thử và thiệp xinh!',
            'created_at' => '2026-11-08'
        ],
        [
            'product_id' => 2,
            'author_name' => 'Thu Hương (Đà Nẵng)',
            'rating' => 5,
            'skin_type' => 'Da hỗn hợp thiên dầu',
            'comment' => 'Kem chống nắng Anessa chuẩn chính hãng 100%. Chất sữa lỏng thấm nhanh, nâng tone nhẹ tự nhiên như thoa cushion, không bị bóng dầu cả ngày. Sẽ tiếp tục ủng hộ shop!',
            'created_at' => '2026-11-06'
        ],
        [
            'product_id' => 3,
            'author_name' => 'Khánh Vy (TP.HCM)',
            'rating' => 5,
            'skin_type' => 'Môi khô, thích tone MLBB',
            'comment' => 'Màu #02 Joyful hồng đào ngọt ngào xinh xỉu các nàng ơi! Chất velvet mướt môi không hề bị lộ vân, mùi thơm nhẹ rất dễ chịu. Giao hàng hỏa tốc trong 2 tiếng.',
            'created_at' => '2026-11-05'
        ],
        [
            'product_id' => 8,
            'author_name' => 'Minh Anh (Cần Thơ)',
            'rating' => 5,
            'skin_type' => 'Da nhạy cảm sau mụn',
            'comment' => 'Mua set Pink Box này làm quà sinh nhật cho bạn thân, bạn khen nức nở. Hộp quà thắt nơ hồng pastel sang xịn mịn, nến thơm dịu, 3 món skincare đều là hàng chuẩn xịn. 10/10 điểm!',
            'created_at' => '2026-11-02'
        ]
    ];
}
