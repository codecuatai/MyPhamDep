<?php
/**
 * DỮ LIỆU MẪU MỸ PHẨM CHÍNH HÃNG - ALICE COSMETIC
 */

function getInitialCategories() {
    return [
        [
            'id' => 'tay-trang',
            'name' => 'Làm Sạch & Tẩy Trang',
            'slug' => 'tay-trang',
            'icon' => 'fa-soap',
            'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=500&q=80',
            'count' => 8,
            'description' => 'Nước tẩy trang, dầu tẩy trang dịu nhẹ lấy sạch bụi mịn và lớp trang điểm.'
        ],
        [
            'id' => 'serum-tinh-chat',
            'name' => 'Tinh Chất & Serum B5',
            'slug' => 'serum-tinh-chat',
            'icon' => 'fa-droplet',
            'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=500&q=80',
            'count' => 12,
            'description' => 'Serum đặc trị mụn thâm, tinh chất phục hồi da mỏng yếu và cấp ẩm căng bóng.'
        ],
        [
            'id' => 'chong-nang',
            'name' => 'Kem Chống Nắng',
            'slug' => 'chong-nang',
            'icon' => 'fa-sun',
            'image' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=500&q=80',
            'count' => 9,
            'description' => 'Kem chống nắng nâng tone tự nhiên, kiềm dầu kháng nước phổ rộng SPF50+.'
        ],
        [
            'id' => 'kem-duong',
            'name' => 'Dưỡng Ẩm & Phục Hồi',
            'slug' => 'kem-duong',
            'icon' => 'fa-spa',
            'image' => 'https://images.unsplash.com/photo-1608248597359-009772c8423e?auto=format&fit=crop&w=500&q=80',
            'count' => 11,
            'description' => 'Khóa ẩm, phục hồi màng bảo vệ da, ngăn ngừa lão hóa và thâm sạm.'
        ],
        [
            'id' => 'makeup-son',
            'name' => 'Son Môi & Makeup',
            'slug' => 'makeup-son',
            'icon' => 'fa-wand-magic-sparkles',
            'image' => 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=500&q=80',
            'count' => 15,
            'description' => 'Son kem lì tone hồng đào, cushion mỏng nhẹ tự nhiên chuẩn Hàn Quốc.'
        ],
        [
            'id' => 'combo-routine',
            'name' => 'Set Quà & Routine',
            'slug' => 'combo-routine',
            'icon' => 'fa-gift',
            'image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=500&q=80',
            'count' => 6,
            'description' => 'Bộ combo skincare hoàn chỉnh tặng kèm nến thơm, hộp nơ hồng & thiệp viết tay.'
        ]
    ];
}

function getInitialProducts() {
    return [
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
                'https://images.unsplash.com/photo-1608248597359-009772c8423e?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Tinh chất La Roche-Posay Effaclar với phức hợp 3 Acid LHA, Salicylic Acid, Glycolic Acid kết hợp cùng Niacinamide giúp giảm mụn sưng đỏ, làm mờ vết thâm mụn chỉ sau 14 ngày, kiểm soát dầu thừa hiệu quả.',
            'ingredients' => 'Aqua / Water, Alcohol Denat., Propanediol, Glycolic Acid, Niacinamide, Dimethyl Isosorbide, Pentylene Glycol, Salicylic Acid, Sodium Hydroxide, PEG-60 Hydrogenated Castor Oil, Hydroxyethylpiperazine Ethane Sulfonic Acid, Citric Acid, PEG-30 Glyceryl Cocoate, Capryloyl Salicylic Acid, Biosaccharide Gum-1, Maltodextrin, Phytic Acid, Polyquaternium-10, Parfum / Fragrance.',
            'how_to_use' => 'Dùng 2-3 giọt vào buổi tối sau bước toner. Vỗ nhẹ khắp mặt và cổ. Luôn dùng kem chống nắng vào ban ngày.',
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
                'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Sữa chống nắng kiềm dầu số 1 Nhật Bản 21 năm liên tiếp. Công nghệ Auto Booster độc quyền tăng cường lớp màng bảo vệ khi gặp Nhiệt độ cao - Nước - Mồ hôi - Độ ẩm. Nâng tone nhẹ nhàng, tự nhiên không bết rít.',
            'ingredients' => 'Chiết xuất trà xanh Uji, Hoa hồng vàng, Collagen, Chiết xuất rễ cây Potentilla, Rễ Cam Thảo dưỡng da suốt cả ngày.',
            'how_to_use' => 'Lắc đều trước khi sử dụng. Thoa đều lên mặt và cổ trước khi ra ngoài 20 phút. Thoa lại sau mỗi 3-4 tiếng nếu hoạt động ngoài trời.',
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
            'ingredients' => 'Dimethicone, Dimethicone Crosspolymer, Polyglyceryl-2 Triisostearate, Diisostearyl Malate, Titanium Dioxide, Dầu hạt Macadamia dưỡng ẩm môi.',
            'how_to_use' => 'Dùng cọ lấy một lượng son vừa đủ, thoa từ lòng môi tán đều ra viền môi. Có thể bặm nhẹ để tạo hiệu ứng ombre ngọt ngào.',
            'is_flash_sale' => 1,
            'flash_sale_stock' => 300,
            'flash_sale_sold' => 268,
            'skin_type' => 'all',
            'variants' => json_encode([
                ['name' => 'Màu #02 Joyful (Cánh Hồng Đào Nữ Tính)', 'price' => 159000, 'original_price' => 220000],
                ['name' => 'Màu #05 Witty (Cam Cháy Vintage)', 'price' => 159000, 'original_price' => 220000],
                ['name' => 'Màu #06 Deepsoul (Đỏ Nâu Trầm Quý Phái)', 'price' => 159000, 'original_price' => 220000],
                ['name' => 'Màu #23 Vintage Taupe (Hồng Nâu Mocha)', 'price' => 165000, 'original_price' => 230000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 104, 'name' => 'Son Dưỡng Hồng Môi Dầu Dừa Bến Tre', 'price' => 19000, 'original_price' => 50000]
            ], JSON_UNESCAPED_UNICODE)
        ],
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
            'description' => 'Nước tẩy trang công nghệ hạt micellar số 1 thế giới. Làm sạch sâu 99% lớp trang điểm và hạt bụi siêu mịn PM 2.5 mà vẫn giữ màng ẩm tự nhiên, không gây cay mắt, không rát da.',
            'ingredients' => 'Aqua/Water, PEG-6 Caprylic/Capric Glycerides, Fructooligosaccharides, Mannitol, Xylitol, Rhamnose, Cucumis Sativus (Cucumber) Fruit Extract, Propylene Glycol, Cetrimonium Bromide, Disodium EDTA.',
            'how_to_use' => 'Thấm đẫm bông tẩy trang, nhẹ nhàng lau khắp mặt đến khi miếng bông sạch hoàn toàn. Không cần rửa lại với nước.',
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
        [
            'id' => 5,
            'name' => 'Tinh Chất Dưỡng Ẩm Phục Hồi Căng Bóng Torriden Dive-In Serum',
            'slug' => 'tinh-chat-torriden-dive-in-serum',
            'brand' => 'Torriden (Hàn Quốc)',
            'category_id' => 'serum-tinh-chat',
            'price' => 319000,
            'original_price' => 450000,
            'discount_percent' => 29,
            'rating' => 4.92,
            'review_count' => 970,
            'sold_count' => 2410,
            'image' => 'https://images.unsplash.com/photo-1608248597359-009772c8423e?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1608248597359-009772c8423e?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Serum dưỡng ẩm quốc dân Hàn Quốc đạt giải quán quân Hwahae 3 năm liên tiếp. Chứa 5 loại Hyaluronic Acid phân tử siêu nhỏ len lỏi sâu vào tế bào da, làm dịu da cháy nắng mẩn đỏ với D-Panthenol và Allantoin.',
            'ingredients' => 'Water, Butylene Glycol, Glycerin, Dipropylene Glycol, 1,2-Hexanediol, Betaine, Panthenol, Hyaluronic Acid thủy phân, Ceramide NP, Malachite Extract.',
            'how_to_use' => 'Nhỏ 3-4 giọt lên da sau khi làm sạch. Vỗ nhẹ cho dưỡng chất thẩm thấu.',
            'is_flash_sale' => 0,
            'flash_sale_stock' => 100,
            'flash_sale_sold' => 45,
            'skin_type' => 'dry',
            'variants' => json_encode([
                ['name' => 'Chai 50ml', 'price' => 319000, 'original_price' => 450000],
                ['name' => 'Chai 100ml (Jumbo size)', 'price' => 529000, 'original_price' => 750000]
            ], JSON_UNESCAPED_UNICODE),
            'add_on_deals' => json_encode([
                ['id' => 102, 'name' => 'Mặt nạ tràm trà giảm viêm mụn Mediheal', 'price' => 12000, 'original_price' => 35000]
            ], JSON_UNESCAPED_UNICODE)
        ],
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
            'description' => 'Kem dưỡng phục hồi da cải tiến với công nghệ vi sinh vật học độc quyền Tribioma, kết hợp 5% Panthenol (Vitamin B5) và Madecassoside giúp làm dịu tức thì cảm giác nóng rát, phục hồi hàng rào bảo vệ da sau peel, laser hay mụn.',
            'ingredients' => 'Aqua / Water, Hydrogenated Polyisobutene, Dimethicone, Glycerin, Butyrospermum Parkii Butter / Shea Butter, Panthenol, Propanediol, Butylene Glycol, Aluminum Starch Octenylsuccinate, Cetyl PEG/PPG-10/1 Dimethicone, Trihydroxystearin, Zinc Gluconate, Madecassoside, Manganese Gluconate.',
            'how_to_use' => 'Thoa ngày 2 lần lên vùng da kích ứng hoặc toàn mặt sau các bước serum.',
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
            'image' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=600&q=80',
            'gallery' => json_encode([
                'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Sản phẩm BHA bán chạy số 1 toàn cầu. Chứa 2% Salicylic Acid tan trong dầu len lỏi dọn sạch bã nhờn sâu trong lỗ chân lông, loại bỏ mụn đầu đen, mụn ẩn và kích thích tái tạo biểu bì mịn màng.',
            'ingredients' => 'Water, Methylpropanediol, Butylene Glycol, Salicylic Acid 2%, Polysorbate 20, Camellia Oleifera Leaf Extract, Sodium Hydroxide, Tetrasodium EDTA.',
            'how_to_use' => 'Dùng 2-3 lần/tuần vào buổi tối. Đổ ra bông lau nhẹ khắp mặt, không rửa lại.',
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
                'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80'
            ], JSON_UNESCAPED_SLASHES),
            'description' => 'Bộ quà tặng giới hạn cao cấp độc quyền tại ALICE Cosmetic. Trọn bộ liệu trình gồm: Nước tẩy trang dịu lành 100ml + Serum Cấp ẩm B5 50ml + Kem dưỡng phục hồi 40ml. Tặng kèm Hộp quà nơ hồng pastel sang trọng + Nến thơm tinh dầu hoa hồng + Thiệp viết tay ý nghĩa gửi người thương!',
            'ingredients' => 'Bộ ba sản phẩm lành tính không hương liệu, không cồn, an toàn tuyệt đối cho mọi làn da kể cả mẹ bầu và da nhạy cảm.',
            'how_to_use' => 'Thứ tự Routine: Tẩy trang sạch -> Serum B5 cấp ẩm -> Kem dưỡng khóa màng bảo vệ.',
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
