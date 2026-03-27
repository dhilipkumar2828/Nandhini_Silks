@extends('frontend.layouts.app')

@section('title', ($product->name ?? 'Product') . ' | Nandhini Silks')

@section('content')
    @push('styles')
        <style>
            * {
                box-sizing: border-box;
            }

            .swiper-wrap-outer {
                position: relative;
                padding: 0 60px;
            }

            .swiper-wrap-outer .swiper-button-next,
            .swiper-wrap-outer .swiper-button-prev {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                margin-top: 0;
                z-index: 1000;
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: #fff;
                box-shadow: 0 10px 22px rgba(169, 27, 67, 0.22);
                color: var(--pink);
                transition: all 0.3s ease;
            }

            .swiper-wrap-outer .swiper-button-next::after,
            .swiper-wrap-outer .swiper-button-prev::after {
                font-size: 18px;
                font-weight: bold;
            }

            .swiper-wrap-outer .swiper-button-prev {
                left: 5px;
            }

            .swiper-wrap-outer .swiper-button-next {
                right: 5px;
            }

            @media (max-width: 768px) {
                .swiper-wrap-outer {
                    padding: 0 15px !important;
                }

                .swiper-wrap-outer .swiper-button-next,
                .swiper-wrap-outer .swiper-button-prev {
                    display: none;
                }
            }

            .attribute-option.active.color-swatch {
                box-shadow: 0 0 0 2px #fff, 0 0 0 4px #A91B43 !important;
                transform: scale(1.15);
                z-index: 2;
            }

            .attribute-option.active.size-btn {
                background: #A91B43 !important;
                color: #fff !important;
                border-color: #A91B43 !important;
                box-shadow: 0 4px 12px rgba(169, 27, 67, 0.2);
            }

            /* Unavailable/Out of Stock Swatch Style - Strike-through & Dashed */
            .attribute-option.unavailable {
                position: relative !important;
                opacity: 0.4 !important;
                cursor: not-allowed !important;
                pointer-events: none !important;
                background: #f8f9fa !important;
                color: #adb5bd !important;
                border: 1px dashed #dee2e6 !important;
                overflow: hidden;
                transition: all 0.3s ease;
            }

            .attribute-option.unavailable::after {
                content: "";
                position: absolute;
                top: 50%;
                left: 10%;
                right: 10%;
                height: 1px;
                background: #adb5bd;
                transform: translateY(-50%);
                z-index: 10;
            }

            /* Diagonal line for color swatches instead of horizontal */
            .attribute-option.unavailable.color-swatch::after {
                left: 0;
                right: 0;
                top: 50%;
                transform: translateY(-50%) rotate(45deg);
            }

            .attribute-option.unavailable.size-btn {
                background-color: #f1f3f5 !important;
            }

            /* Override cache for thumbnails */
            .product-thumbnails {
                display: flex !important;
                flex-direction: column !important;
                gap: 12px !important;
                width: 70px !important;
                overflow-y: auto;
                max-height: 500px;
            }

            .product-thumbnails::-webkit-scrollbar {
                width: 3px;
            }

            .product-thumbnails::-webkit-scrollbar-thumb {
                background: #ddd;
                border-radius: 10px;
            }

            .thumbnail {
                width: 70px !important;
                height: 87.5px !important;
                /* 4:5 ratio */
                border-radius: 6px !important;
                overflow: hidden;
                border: 2px solid transparent !important;
                cursor: pointer;
                transition: border-color 0.2s ease, transform 0.2s ease;
                flex-shrink: 0;
                background: #f9f9f9;
            }

            .thumbnail.active,
            .thumbnail:hover {
                border-color: #A91B43 !important;
                transform: scale(1.02);
            }

            .thumbnail img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            @media (max-width: 900px) {
                #zoomContainer {
                    position: relative !important;
                    overflow: hidden !important;
                    left: 0 !important;
                    margin: 0 auto !important;
                }

                #zoomContainer img,
                #mainImg {
                    width: 100% !important;
                    height: 100% !important;
                    object-fit: cover !important;
                    object-position: center center !important;
                    transform: none !important;
                }

                .product-thumbnails {
                    flex-direction: row !important;
                    width: 100% !important;
                    max-height: auto;
                    overflow-x: auto;
                    padding-bottom: 10px;
                }
            }

            .review-empty-state {
                text-align: center;
                padding: 56px 24px;
                background: linear-gradient(180deg, #fffdf8 0%, #fff7ef 100%);
                border: 1px solid rgba(169, 27, 67, 0.12);
                border-radius: 28px;
                box-shadow: 0 14px 32px rgba(169, 27, 67, 0.06);
            }

            .review-empty-icon {
                width: 68px;
                height: 68px;
                margin: 0 auto 18px;
                border-radius: 22px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #fff;
                color: #a91b43;
                box-shadow: 0 10px 20px rgba(169, 27, 67, 0.12);
                font-size: 28px;
            }

            .review-empty-title {
                margin: 0 0 8px;
                color: #1f2937;
                font-size: 18px;
                font-weight: 700;
            }

            .review-empty-text {
                margin: 0 0 24px;
                color: #6b7280;
                font-size: 14px;
                line-height: 1.6;
            }

            .review-write-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                min-height: 46px;
                padding: 0 24px;
                border: none;
                border-radius: 999px;
                background: linear-gradient(90deg, #a91b43 0%, #c62355 100%);
                color: #fff;
                font-size: 12px;
                font-weight: 800;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                cursor: pointer;
                box-shadow: 0 12px 24px rgba(169, 27, 67, 0.22);
                transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            }

            .review-write-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 16px 28px rgba(169, 27, 67, 0.28);
                filter: brightness(1.02);
            }

            .review-write-btn:active {
                transform: translateY(0);
            }

            .review-inline-panel {
                display: none;
                width: min(100%, 560px);
                background: #fff;
                border-radius: 24px;
                padding: 28px;
                border: 1px solid rgba(169, 27, 67, 0.12);
                box-shadow: 0 18px 36px rgba(169, 27, 67, 0.08);
                margin-top: 24px;
            }

            .review-inline-panel.open {
                display: block;
            }

            .review-entry-state.hidden {
                display: none;
            }

            .review-modal-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                margin-bottom: 20px;
            }

            .review-modal-title {
                margin: 0;
                font-size: 22px;
                font-weight: 700;
                color: #1f2937;
            }

            .review-modal-close {
                width: 40px;
                height: 40px;
                border: none;
                border-radius: 999px;
                background: #f8e8ee;
                color: #a91b43;
                font-size: 20px;
                cursor: pointer;
            }

            .review-form-group {
                margin-bottom: 18px;
            }

            .review-form-label {
                display: block;
                margin-bottom: 8px;
                color: #374151;
                font-size: 14px;
                font-weight: 600;
            }

            .review-form-select,
            .review-form-textarea {
                width: 100%;
                border: 1px solid #e5e7eb;
                border-radius: 14px;
                background: #fff;
                color: #111827;
                font-size: 14px;
                outline: none;
                transition: border-color 0.2s ease, box-shadow 0.2s ease;
            }

            .review-form-select {
                height: 48px;
                padding: 0 14px;
            }

            .review-form-textarea {
                min-height: 150px;
                padding: 14px;
                resize: vertical;
                line-height: 1.6;
            }

            .review-form-select:focus,
            .review-form-textarea:focus {
                border-color: #a91b43;
                box-shadow: 0 0 0 3px rgba(169, 27, 67, 0.12);
            }

            .review-form-actions {
                display: flex;
                justify-content: flex-end;
                gap: 12px;
                margin-top: 24px;
            }

            .review-form-cancel,
            .review-form-submit {
                min-height: 46px;
                padding: 0 22px;
                border-radius: 999px;
                font-size: 13px;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .review-form-cancel {
                border: 1px solid #e5e7eb;
                background: #fff;
                color: #4b5563;
            }

            .review-form-submit {
                border: none;
                background: linear-gradient(90deg, #a91b43 0%, #c62355 100%);
                color: #fff;
                box-shadow: 0 12px 24px rgba(169, 27, 67, 0.2);
            }

            /* Enhanced Swatch Container */
            .swatch-container {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
                align-items: center;
                padding: 2px 0 0;
            }

            .attribute-option {
                position: relative;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .product-info-details {
                display: flex;
                flex-direction: column;
                gap: 14px;
            }

            .product-meta {
                margin-bottom: 0 !important;
            }

            .product-brand {
                margin: 0 0 3px !important;
            }

            .product-meta-item {
                margin: 0 !important;
                line-height: 1.35;
            }

            .product-title-detail {
                margin: 0 !important;
            }

            .product-summary-row {
                margin: 0 !important;
            }

            .product-tax-note {
                margin: -4px 0 0 !important;
                line-height: 1.35;
            }

            .product-description-short {
                margin-bottom: 0 !important;
            }

            .product-selections {
                margin-bottom: 0 !important;
            }

            .product-actions {
                display: flex !important;
                flex-direction: column;
                gap: 14px;
            }

            .attribute-section {
                margin-bottom: 14px !important;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .attribute-section:last-child {
                margin-bottom: 0 !important;
            }

            .attribute-title {
                margin-bottom: 0 !important;
                line-height: 1.35;
                justify-content: flex-start;
            }

            .size-btn {
                padding: 6px 14px !important;
                border-radius: 9px !important;
                font-size: 11px !important;
                min-width: 44px !important;
                min-height: 36px !important;
                line-height: 1 !important;
            }

            .color-swatch {
                width: 32px !important;
                height: 32px !important;
            }

            .color-swatch:hover {
                transform: scale(1.1);
            }

            .size-btn:hover:not(.active) {
                border-color: #A91B43 !important;
                color: #A91B43 !important;
                background: #fff5f8 !important;
            }

            .quantity-section {
                width: 100%;
                margin-bottom: 0 !important;
            }

            .quantity-title {
                margin-bottom: 8px !important;
            }

            .product-actions-group {
                margin-top: 0 !important;
                align-items: stretch;
                gap: 12px !important;
            }

            .btn-add-cart,
            .btn-buy-now {
                height: 46px !important;
                border-radius: 10px !important;
                font-size: 15px !important;
                font-weight: 5  00 !important;
                font-family: "Plus Jakarta Sans", "Outfit", "Instrument Sans", "Segoe UI", "Times New Roman", sans-serif !important;
                letter-spacing: 0.5px !important;
                gap: 7px !important;
                padding: 0 16px !important;
            }

            .btn-add-cart i,
            .btn-buy-now i {
                font-size: 13px !important;
            }

            .quantity-picker {
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center;
                min-height: 48px;
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                overflow: hidden;
            }

            .qty-btn {
                width: 44px;
                height: 48px;
                padding: 0 !important;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px !important;
                color: #1a1a1a;
                background: #f9fafb;
                border: none;
                cursor: pointer;
                transition: all 0.2s;
            }

            .qty-btn:hover {
                background: #f3f4f6;
                color: #A91B43;
            }

            .quantity-picker {
                min-height: 42px !important;
                height: 42px !important;
            }

            .qty-btn {
                height: 42px !important;
                width: 40px !important;
            }

            .qty-input {
                height: 42px !important;
                width: 40px !important;
            }

            .qty-input {
                width: 44px !important;
                height: 48px;
                padding: 0;
                text-align: center;
                background: #fff;
                border: none;
                border-left: 1px solid #e5e7eb;
                border-right: 1px solid #e5e7eb;
                font-size: 15px !important;
                font-weight: 700;
                color: #1a1a1a;
            }

            .product-detail-page {
                background: #fafafa;
                padding: 24px 0 64px;
                overflow-x: hidden;
                max-width: 100vw;
            }

            .product-detail-grid {
                max-width: 1400px;
                margin: 0 auto;
                grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr) !important;
                gap: 50px !important;
                padding: 40px !important;
                border-radius: 20px !important;
                background: #fff !important;
                border: 1px solid #eee;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03) !important;
                align-items: flex-start;
            }

            .product-gallery {
                gap: 18px !important;
                padding: 12px;
                border-radius: 16px;
                background: #fff;
                border: 1px solid #f0f0f0;
            }

            .main-product-image,
            #zoomContainer {
                border-radius: 24px !important;
                background: #f7efe7 !important;
                box-shadow: 0 20px 40px rgba(110, 66, 36, 0.08);
            }

            .main-product-image img,
            #mainImg {
                border-radius: 24px;
            }

            .btn-wishlist-detail {
                width: 52px !important;
                height: 52px !important;
                border-radius: 50% !important;
                background: rgba(255, 255, 255, 0.96) !important;
                box-shadow: 0 16px 34px rgba(0, 0, 0, 0.12) !important;
            }

            .product-thumbnails {
                gap: 10px !important;
                width: 78px !important;
            }

            .thumbnail {
                width: 78px !important;
                height: 96px !important;
                border-radius: 14px !important;
                border: 1px solid rgba(169, 27, 67, 0.1) !important;
                background: #fff !important;
            }

            .thumbnail.active,
            .thumbnail:hover {
                border-color: #a91b43 !important;
                box-shadow: 0 12px 24px rgba(169, 27, 67, 0.16);
                transform: translateY(-2px);
            }

            .product-info-details {
                gap: 18px !important;
                padding: 8px 0;
            }

            .product-meta {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 10px 12px;
            }

            .product-brand {
                display: inline-flex;
                align-items: center;
                min-height: 24px;
                padding: 0 0px;
                background: none;
                color: #a91b43 !important;
                font-size: 11px !important;
                font-weight: 800 !important;
                letter-spacing: 0.1em !important;
                text-transform: uppercase;
            }

            .product-meta-item {
                display: inline-flex;
                align-items: center;
                min-height: 24px;
                padding: 0 0px;
                background: none;
                color: #667085 !important;
                font-size: 11px !important;
                font-weight: 700 !important;
            }

            .product-title-detail {
                font-size: clamp(24px, 3.5vw, 32px) !important;
                line-height: 1.1 !important;
                letter-spacing: -0.02em;
                color: #161616 !important;
                max-width: none;
            }

            .product-summary-row {
                gap: 12px 18px !important;
                padding-bottom: 4px;
            }

            .product-rating {
                padding: 0;
                gap: 10px !important;
            }

            .stars {
                display: inline-flex;
                gap: 2px;
                font-size: 13px;
            }

            .product-price-section {
                gap: 10px !important;
                flex-wrap: wrap;
            }

            .current-price {
                font-size: clamp(28px, 4vw, 34px) !important;
                letter-spacing: -0.03em;
                line-height: 1;
            }

            .old-price {
                font-size: 19px !important;
                font-weight: 500;
            }

            .discount-badge {
                padding: 3px 8px !important;
                border-radius: 3px !important;
                background: #a91b43 !important;
                color: #fff !important;
                font-size: 9px !important;
                font-weight: 800;
                letter-spacing: 0.04em;
            }

            .product-tax-note {
                font-size: 11px !important;
                color: #999 !important;
                margin-top: -6px !important;
            }

            .stock-badge {
                display: inline-flex;
                align-items: center;
                min-height: 26px;
                padding: 0 10px !important;
                border-radius: 4px !important;
                font-size: 9px !important;
                font-weight: 800 !important;
                letter-spacing: 0.05em;
                text-transform: uppercase;
            }

            .stock-in {
                background: #e8f5e9 !important;
                color: #2e7d32 !important;
                border: 1px solid #c8e6c9;
            }

            .stock-out {
                background: #ffebee !important;
                color: #c62828 !important;
                border: 1px solid #ffcdd2;
            }

            .product-description-short {
                max-width: 58ch !important;
                font-size: 15px !important;
                line-height: 1.75 !important;
                color: #5f5f68 !important;
            }

            .product-actions {
                gap: 18px !important;
                width: 100%;
            }

            .product-selections {
                padding: 0;
                border-radius: 0;
                background: none;
                border: none;
            }

            .attribute-section {
                margin-bottom: 18px !important;
                gap: 10px !important;
            }

            .attribute-title {
                font-size: 11px !important;
                letter-spacing: 0.1em !important;
                color: #667085 !important;
                text-transform: uppercase;
                font-weight: 700 !important;
            }

            .attribute-title span {
                display: inline;
                background: none;
                padding: 0;
                color: #1a1a1a;
                font-weight: 800;
            }

            .swatch-container {
                gap: 10px !important;
                padding: 0 !important;
            }

            .color-swatch {
                width: 32px !important;
                height: 32px !important;
                box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.08) !important;
            }

            .size-btn {
                min-height: 34px !important;
                padding: 4px 14px !important;
                border-radius: 4px !important;
                font-size: 11.5px !important;
                background: #fff !important;
                border: 1px solid #e2e8f0 !important;
                box-shadow: none !important;
            }

            .quantity-section {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .quantity-title {
                font-size: 12px !important;
                letter-spacing: 0.14em !important;
            }

            .quantity-picker {
                height: 36px !important;
                border-radius: 8px !important;
                border-color: #e2e8f0 !important;
                box-shadow: none !important;
            }

            .product-actions-group {
                max-width: none !important;
                gap: 14px !important;
            }

            .btn-add-cart,
            .btn-buy-now {
                min-height: 42px !important;
                height: 42px !important;
                border-radius: 6px !important;
                font-size: 12px !important;
                font-family: "Plus Jakarta Sans", "Outfit", "Instrument Sans", "Segoe UI", "Times New Roman", sans-serif !important;
                font-weight: 600 !important;
                letter-spacing: 0.06em !important;
                text-transform: uppercase;
                box-shadow: none !important;
            }

            .btn-add-cart {
                background: linear-gradient(135deg, #a91b43 0%, #cb2f62 100%) !important;
            }

            .btn-buy-now {
                background: linear-gradient(135deg, #232323 0%, #111111 100%) !important;
            }

            /* Tabs Polish */
            .product-extra-info {
                margin-top: 48px;
                border-top: 1px solid #eee;
            }

            .tabs-info {
                display: flex;
                gap: 32px;
                border-bottom: 2px solid #f5f5f5;
            }

            .tab-btn {
                background: none;
                border: none;
                padding: 16px 0;
                font-size: 13px;
                font-weight: 700;
                color: #667085;
                cursor: pointer;
                position: relative;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                transition: all 0.2s ease;
            }

            .tab-btn.active {
                color: #a91b43;
            }

            .tab-btn.active::after {
                content: "";
                position: absolute;
                bottom: -2px;
                left: 0;
                width: 100%;
                height: 2px;
                background: #a91b43;
            }

            .tab-pane {
                display: none;
                padding: 32px 0;
                animation: fadeIn 0.4s ease;
            }

            .tab-pane.active {
                display: block;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .tab-content-text {
                color: #5f5f68;
                line-height: 1.65;
                font-size: 14px;
                max-width: 720px;
                width: 100%;
                min-width: 0;
                overflow-wrap: anywhere;
                word-break: break-word;
            }

            .tab-content-text p {
                margin-bottom: 10px;
            }

            .tab-content-text ul {
                padding-left: 18px;
                list-style-type: disc;
            }

            .tab-content-text li {
                margin-bottom: 4px;
            }

            .tab-content-text > * {
                max-width: 100%;
            }

            .tab-content-text img,
            .tab-content-text video,
            .tab-content-text canvas,
            .tab-content-text svg {
                display: block;
                max-width: 100% !important;
                height: auto !important;
                border-radius: 12px;
            }

            .tab-content-text iframe,
            .tab-content-text embed,
            .tab-content-text object {
                display: block;
                width: 100% !important;
                max-width: 100% !important;
                min-height: 240px;
                border: 0;
                border-radius: 12px;
            }

            .tab-content-text table {
                width: 100% !important;
                max-width: 100% !important;
                table-layout: fixed;
                border-collapse: collapse;
                display: block;
                overflow-x: auto;
            }

            .tab-content-text th,
            .tab-content-text td {
                padding: 8px 10px;
                vertical-align: top;
                white-space: normal;
                word-break: break-word;
                font-size: 13px;
            }

            .tab-content-text a {
                overflow-wrap: anywhere;
            }

            .tab-content-text pre,
            .tab-content-text code {
                max-width: 100%;
                overflow-x: auto;
                white-space: pre-wrap;
                word-break: break-word;
            }

            .specs-table {
                width: 100%;
                max-width: 600px;
                border-collapse: collapse;
            }

            .specs-table td {
                padding: 10px 14px;
                border-bottom: 1px solid #f5f5f5;
                font-size: 13px;
            }

            .specs-label {
                width: 200px;
                font-weight: 600;
                color: #667085;
                background: #fafafa;
            }

            .specs-value {
                color: #1a1a1a;
                font-weight: 500;
            }

            .related-products {
                margin-top: 64px;
                padding-top: 48px;
                border-top: 1px solid #eee;
            }

            .related-title {
                font-size: 24px;
                font-weight: 800;
                color: #1a1a1a;
                margin-bottom: 32px;
                text-align: center;
            }

            .share-section {
                margin-top: 18px !important;
                padding-top: 18px !important;
                border-top: 1px solid rgba(15, 23, 42, 0.05) !important;
            }

            .share-title {
                font-size: 11px !important;
                font-weight: 700;
                color: #667085 !important;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                display: block;
                margin-bottom: 8px;
            }

            .share-links {
                display: flex;
                gap: 12px;
            }

            .share-links a {
                color: #1a1a1a;
                transition: color 0.2s;
            }

            .share-links a:hover {
                color: #a91b43;
            }

            .delivery-check {
                margin-top: 15px !important;
                background: #fff !important;
                padding: 12px 14px !important;
                border-radius: 12px !important;
                border: 1px solid rgba(15, 23, 42, 0.08) !important;
                max-width: 100% !important;
                box-shadow: none !important;
            }

            .delivery-title {
                font-size: 11px !important;
                font-weight: 700;
                color: #667085 !important;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-bottom: 8px !important;
            }

            .pincode-input-group {
                display: flex;
                gap: 8px;
            }

            .pincode-input {
                flex: 1;
                padding: 0 12px;
                height: 38px;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                font-size: 12.5px;
            }

            .btn-pincode {
                background: #1a1a1a;
                color: #fff;
                border: none;
                padding: 0 16px;
                height: 38px;
                border-radius: 8px;
                font-size: 11.5px;
                font-weight: 700;
                cursor: pointer;
                transition: background 0.2s;
            }

            .btn-pincode:hover {
                background: #000;
            }

            .delivery-note {
                font-size: 10px !important;
                color: #667085 !important;
                margin-top: 6px !important;
                margin-bottom: 0 !important;
            }



            .product-extra-info {
                margin-top: 34px !important;
                padding: 28px !important;
                border-radius: 28px !important;
                background: linear-gradient(180deg, #ffffff 0%, #fffaf6 100%) !important;
                border: 1px solid rgba(169, 27, 67, 0.08);
                box-shadow: 0 18px 50px rgba(15, 23, 42, 0.06) !important;
            }

            .tabs-info {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                padding-bottom: 18px;
                border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            }

            .tab-btn {
                position: relative;
                min-height: 44px;
                padding: 0 18px !important;
                border: 1px solid rgba(15, 23, 42, 0.08);
                border-radius: 999px !important;
                background: #fff !important;
                color: #374151 !important;
                font-size: 13px !important;
                font-weight: 700 !important;
                transition: all 0.25s ease;
            }

            .tab-btn.active {
                background: linear-gradient(135deg, #a91b43 0%, #cb2f62 100%) !important;
                color: #fff !important;
                border-color: transparent !important;
                box-shadow: 0 14px 28px rgba(169, 27, 67, 0.2);
            }

            .tab-btn.active::after {
                display: none !important;
            }

            .tab-pane {
                padding-top: 26px !important;
            }

            /* Hero Section Alignment Refinement */
            .product-detail-grid {
                justify-content: space-between;
                gap: 28px !important;
            }

            .product-gallery {
                align-self: start;
            }

            .product-info-details {
                width: 100%;
                max-width: 100%;
                justify-self: start;
                gap: 12px !important;
            }

            .product-title-detail {
                max-width: none !important;
                margin-bottom: 4px !important;
                width: 100% !important;
            }

            .product-summary-row {
                display: grid !important;
                grid-template-columns: 1fr;
                gap: 10px !important;
                align-items: start !important;
            }

            .product-rating,
            .product-price-section,
            .stock-status {
                width: fit-content;
            }

            .product-price-section {
                gap: 8px !important;
            }

            .product-tax-note {
                margin-top: 0 !important;
            }

            .product-description-short {
                max-width: 100% !important;
            }

            .product-actions {
                gap: 16px !important;
            }

            .attribute-section {
                margin-bottom: 14px !important;
                gap: 8px !important;
            }

            .quantity-section {
                gap: 6px !important;
            }

            .quantity-picker {
                width: 110px !important;
                max-width: 110px !important;
            }

            .product-actions-group {
                display: grid !important;
                gap: 12px !important;
                max-width: 30% !important;
            }

            .btn-add-cart,
            .btn-buy-now {
                width: 90% !important;
                justify-content: center;
            }

            .share-section {
                margin-top: 8px !important;
                padding-top: 14px !important;
                display: flex;
                flex-direction: row;
                align-items: center;
                gap: 12px;
            }

            .share-title {
                margin: 0 !important;
                min-width: fit-content;
            }

            .delivery-check {
                width: 100%;
                margin-top: 10px !important;
            }

            @media (min-width: 768px) {
                .product-detail-grid {
                    align-items: start !important;
                }

                .product-gallery {
                    position: sticky !important;
                    top: 24px !important;
                    align-self: start !important;
                }

                .product-info-details {
                    min-width: 0 !important;
                }

                .product-actions-group {
                    grid-template-columns: repeat(2, minmax(180px, 220px)) !important;
                    justify-content: start !important;
                    width: auto !important;
                    max-width: max-content !important;
                }

                .btn-add-cart,
                .btn-buy-now {
                    width: 90% !important;
                    min-width: 180px !important;
                }
            }

            @media (max-width: 768px) {
                .product-detail-page {
                    padding: 10px 0 40px !important;
                    background: #f7f4ef !important;
                    overflow-x: hidden !important;
                }

                .page-shell {
                    margin-left: 0 !important;
                    margin-right: 0 !important;
                    padding-left: 12px;
                    padding-right: 12px;
                    overflow-x: hidden !important;
                    width: 100% !important;
                    max-width: 100% !important;
                    box-sizing: border-box !important;
                }

                .breadcrumb {
                    margin-bottom: 10px;
                    font-size: 12px;
                    line-height: 1.5;
                    white-space: normal;
                    padding-left: 2px;
                }

                .product-detail-grid {
                    grid-template-columns: 1fr !important;
                    gap: 14px !important;
                    padding: 14px 12px !important;
                    border-radius: 18px !important;
                    width: 100% !important;
                    max-width: 100% !important;
                    overflow: hidden !important;
                    margin: 0 auto !important;
                    box-sizing: border-box !important;
                }

                .product-gallery {
                    display: flex !important;
                    flex-direction: column !important;
                    position: static !important;
                    top: auto !important;
                    gap: 10px !important;
                    align-items: stretch !important;
                    padding: 0;
                    border-radius: 0;
                    width: 100% !important;
                    max-width: 100% !important;
                    overflow: hidden !important;
                }

                .main-product-image,
                #zoomContainer {
                    width: 100% !important;
                    max-width: none !important;
                    margin: 0 auto !important;
                    aspect-ratio: 4 / 5 !important;
                    max-height: 56vh !important;
                    overflow: hidden !important;
                    background: #faf7f3 !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    padding: 0 !important;
                }

                .main-product-image,
                #zoomContainer,
                #zoomContainer img,
                #mainImg {
                    border-radius: 14px !important;
                }

                #zoomContainer img,
                #mainImg {
                    width: 100% !important;
                    height: 100% !important;
                    object-fit: contain !important;
                    object-position: center top !important;
                }

                .product-thumbnails {
                    display: flex !important;
                    flex-direction: row !important;
                    justify-content: flex-start !important;
                    align-items: center !important;
                    gap: 8px !important;
                    padding-top: 0 !important;
                    padding-bottom: 2px !important;
                    width: 100% !important;
                    max-width: 100% !important;
                    overflow-x: auto !important;
                    overflow-y: hidden !important;
                    -webkit-overflow-scrolling: touch;
                    scrollbar-width: none;
                }

                .product-thumbnails::-webkit-scrollbar {
                    display: none;
                }

                .thumbnail {
                    width: 54px !important;
                    height: 68px !important;
                    flex: 0 0 auto !important;
                }

                .product-info-details {
                    gap: 12px;
                    width: 100%;
                    align-items: stretch;
                    padding: 0 !important;
                    max-width: none;
                    justify-self: stretch;
                }

                .product-title-detail {
                    font-size: clamp(18px, 6vw, 24px) !important;
                    line-height: 1.18 !important;
                    max-width: none;
                }

                .product-meta {
                    gap: 6px 8px !important;
                }

                .product-brand,
                .product-meta-item,
                .attribute-title,
                .quantity-title,
                .share-title,
                .delivery-title {
                    letter-spacing: 0.08em !important;
                }

                .product-summary-row {
                    gap: 4px 10px !important;
                    align-items: flex-start !important;
                }

                .product-price-section {
                    width: 100%;
                    flex-wrap: wrap;
                    gap: 4px 6px !important;
                    align-items: center !important;
                }

                .product-description-short {
                    max-width: 100% !important;
                    font-size: 12px !important;
                    line-height: 1.5 !important;
                }

                .product-selections {
                    padding: 0;
                    border-radius: 14px;
                    width: 100% !important;
                    max-width: 100% !important;
                    overflow: hidden !important;
                }

                .attribute-title {
                    flex-wrap: wrap;
                    gap: 4px 8px !important;
                    line-height: 1.4;
                }

                .swatch-container {
                    gap: 8px;
                }

                .quantity-section {
                    margin-bottom: 16px !important;
                    align-items: flex-start !important;
                }

                .quantity-picker {
                    width: 120px !important;
                    max-width: 120px;
                }

                .product-actions-group {
                    display: flex !important;
                    flex-direction: column !important;
                    width: 100% !important;
                    max-width: 100% !important;
                    gap: 12px !important;
                }

                .product-actions-group > * {
                    width: 100% !important;
                    flex: 1 1 auto !important;
                }

                .btn-add-cart,
                .btn-buy-now {
                    height: 46px !important;
                    min-height: 46px !important;
                    border-radius: 10px !important;
                    font-size: 11px !important;
                    letter-spacing: 0.4px !important;
                    gap: 6px !important;
                    padding: 0 16px !important;
                    width: 100% !important;
                    justify-content: center !important;
                }

                .share-section {
                    flex-direction: row;
                    flex-wrap: wrap;
                    align-items: center;
                    gap: 8px;
                    padding-top: 12px !important;
                }

                .delivery-check {
                    padding: 12px !important;
                    border-radius: 14px !important;
                    width: 100% !important;
                    max-width: 100% !important;
                }

                .pincode-input-group {
                    flex-direction: column;
                    gap: 10px;
                }

                .btn-pincode {
                    width: 100%;
                }

                .product-extra-info {
                    margin-top: 24px !important;
                    padding: 14px 12px !important;
                    border-radius: 18px !important;
                    width: 100% !important;
                    max-width: 100% !important;
                    box-sizing: border-box !important;
                }

                .tabs-info {
                    display: grid !important;
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 10px !important;
                    padding-bottom: 12px;
                    overflow: visible;
                    -webkit-overflow-scrolling: auto;
                    flex-wrap: wrap !important;
                    scrollbar-width: auto;
                }

                .tab-btn {
                    width: 100%;
                    min-width: 0;
                    justify-content: center;
                    padding: 10px 12px;
                    font-size: 11px;
                    flex-shrink: 1;
                    text-align: center;
                    white-space: normal;
                    line-height: 1.25;
                }

                .tab-pane {
                    padding-top: 18px !important;
                }

                .tab-content-text {
                    font-size: 13px !important;
                    line-height: 1.6 !important;
                }

                .tab-content-text img,
                .tab-content-text video,
                .tab-content-text iframe,
                .tab-content-text embed,
                .tab-content-text object,
                .tab-content-text table {
                    width: 100% !important;
                    max-width: 100% !important;
                }

                .tab-content-text iframe,
                .tab-content-text embed,
                .tab-content-text object {
                    min-height: 200px;
                }

                .specs-table {
                    display: block;
                    overflow-x: auto;
                    white-space: nowrap;
                }

                .specs-table tbody,
                .specs-table tr {
                    width: 100%;
                }

                .specs-table td {
                    padding: 8px 10px !important;
                    font-size: 12px !important;
                    white-space: normal;
                }

                .review-empty-state {
                    padding: 42px 18px;
                    border-radius: 22px;
                }

                .review-empty-title {
                    font-size: 16px;
                }

                .review-empty-text {
                    font-size: 13px;
                }

                .review-write-btn {
                    width: 100%;
                }

                .review-inline-panel {
                    padding: 22px 18px;
                    border-radius: 20px;
                }

                .review-form-actions {
                    flex-direction: column;
                }

                .review-form-cancel,
                .review-form-submit {
                    width: 100%;
                }
            }

            /* Final PDP responsive system - mobile only */
            @media (max-width: 767px) {
            .product-detail-page {
                --pdp-shell: clamp(12px, 4vw, 24px);
                --pdp-gap: clamp(14px, 3.8vw, 24px);
                --pdp-card-radius: clamp(18px, 4vw, 28px);
                --pdp-soft-radius: clamp(12px, 3vw, 20px);
                --pdp-card-padding: clamp(14px, 4vw, 28px);
                --pdp-surface: #ffffff;
                --pdp-border: rgba(17, 24, 39, 0.08);
                --pdp-muted: #6b7280;
                --pdp-heading: #151515;
                --pdp-accent: #a91b43;
                --pdp-accent-strong: #8f1239;
                --pdp-soft-bg: #fffaf6;
                background: linear-gradient(180deg, #fbf7f2 0%, #f8f4ee 100%) !important;
                padding: clamp(12px, 3vw, 24px) 0 clamp(28px, 7vw, 64px) !important;
                overflow-x: clip !important;
            }

            .product-detail-page .page-shell {
                width: min(100%, 1240px) !important;
                margin-inline: auto !important;
                padding-inline: var(--pdp-shell) !important;
                box-sizing: border-box !important;
            }

            .product-detail-page .breadcrumb {
                margin: 0 0 clamp(10px, 2.6vw, 18px) !important;
                padding-inline: 2px;
                font-size: clamp(11px, 2.9vw, 13px) !important;
                line-height: 1.5 !important;
                color: var(--pdp-muted) !important;
                white-space: normal !important;
                word-break: break-word;
            }

            .product-detail-page .breadcrumb a,
            .product-detail-page .breadcrumb span {
                display: inline;
            }

            .product-detail-page .product-detail-grid {
                display: grid !important;
                grid-template-columns: minmax(0, 1fr) !important;
                gap: var(--pdp-gap) !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 auto !important;
                padding: var(--pdp-card-padding) !important;
                background: rgba(255, 255, 255, 0.96) !important;
                border: 1px solid var(--pdp-border) !important;
                border-radius: var(--pdp-card-radius) !important;
                box-shadow: 0 20px 44px rgba(15, 23, 42, 0.06) !important;
                align-items: start !important;
                overflow: clip !important;
                box-sizing: border-box !important;
            }

            .product-detail-page .product-gallery {
                display: flex !important;
                flex-direction: column !important;
                gap: clamp(10px, 2.8vw, 16px) !important;
                position: relative !important;
                top: auto !important;
                padding: 0 !important;
                margin: 0 !important;
                min-width: 0 !important;
                border: 0 !important;
                background: transparent !important;
                box-shadow: none !important;
            }

            .product-detail-page .main-product-image,
            .product-detail-page #zoomContainer {
                width: 100% !important;
                aspect-ratio: 4 / 5 !important;
                min-height: 0 !important;
                max-height: min(74vw, 560px) !important;
                margin: 0 !important;
                padding: clamp(4px, 1.5vw, 10px) !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                overflow: hidden !important;
                border-radius: clamp(16px, 4vw, 26px) !important;
                box-sizing: border-box !important;
                box-shadow: inset 0 0 0 1px rgba(110, 66, 36, 0.06), 0 14px 34px rgba(110, 66, 36, 0.1) !important;
            }

            .product-detail-page .main-product-image img,
            .product-detail-page #zoomContainer img,
            .product-detail-page #mainImg {
                width: 120% !important;
                height: 120% !important;
                max-width: 120% !important;
                object-fit: contain !important;
                object-position: center center !important;
                border-radius: calc(clamp(16px, 4vw, 26px) - 4px) !important;
                display: block !important;
            }

            .product-detail-page .thumbnail img {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                object-position: center center !important;
            }

            .product-detail-page .btn-wishlist-detail {
                width: clamp(40px, 10vw, 52px) !important;
                height: clamp(40px, 10vw, 52px) !important;
                top: clamp(10px, 3vw, 16px) !important;
                right: clamp(10px, 3vw, 16px) !important;
            }

            .product-detail-page .product-thumbnails {
                display: flex !important;
                flex-direction: row !important;
                gap: clamp(8px, 2.4vw, 12px) !important;
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                padding: 0 0 2px !important;
                overflow-x: auto !important;
                overflow-y: hidden !important;
                scrollbar-width: none;
                -webkit-overflow-scrolling: touch;
            }

            .product-detail-page .product-thumbnails::-webkit-scrollbar {
                display: none;
            }

            .product-detail-page .thumbnail {
                width: clamp(56px, 15vw, 82px) !important;
                height: auto !important;
                aspect-ratio: 4 / 5 !important;
                flex: 0 0 auto !important;
                border-radius: clamp(10px, 2.5vw, 14px) !important;
                overflow: hidden !important;
            }

            .product-detail-page .product-info-details {
                display: flex !important;
                flex-direction: column !important;
                gap: clamp(10px, 2.8vw, 18px) !important;
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .product-detail-page .product-meta {
                display: flex !important;
                flex-wrap: wrap !important;
                gap: 6px 10px !important;
                align-items: center !important;
                margin: 0 !important;
            }

            .product-detail-page .product-brand,
            .product-detail-page .product-meta-item {
                min-height: auto !important;
                font-size: clamp(10px, 2.7vw, 12px) !important;
                letter-spacing: 0.08em !important;
                line-height: 1.4 !important;
            }

            .product-detail-page .product-title-detail {
                margin: 0 !important;
                width: 100% !important;
                max-width: 18ch !important;
                font-size: clamp(1.5rem, 5.8vw, 2.15rem) !important;
                line-height: 1.08 !important;
                letter-spacing: -0.03em !important;
                color: var(--pdp-heading) !important;
                text-wrap: balance;
            }

            .product-detail-page .product-summary-row {
                display: flex !important;
                flex-wrap: wrap !important;
                align-items: center !important;
                gap: 8px 14px !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .product-detail-page .product-rating,
            .product-detail-page .product-price-section,
            .product-detail-page .stock-status {
                min-width: 0 !important;
            }

            .product-detail-page .product-rating {
                gap: 8px !important;
                font-size: clamp(12px, 3.2vw, 14px) !important;
            }

            .product-detail-page .stars {
                font-size: clamp(12px, 3vw, 14px) !important;
            }

            .product-detail-page .product-price-section {
                display: flex !important;
                flex-wrap: wrap !important;
                align-items: center !important;
                gap: 4px 8px !important;
            }

            .product-detail-page .current-price {
                font-size: clamp(1.9rem, 7vw, 2.4rem) !important;
                line-height: 1 !important;
            }

            .product-detail-page .old-price {
                font-size: clamp(1rem, 4vw, 1.2rem) !important;
            }

            .product-detail-page .discount-badge,
            .product-detail-page .stock-badge {
                font-size: clamp(9px, 2.2vw, 11px) !important;
            }

            .product-detail-page .product-tax-note {
                margin: -4px 0 0 !important;
                font-size: clamp(10px, 2.8vw, 12px) !important;
                line-height: 1.45 !important;
            }

            .product-detail-page .product-description-short {
                max-width: 100% !important;
                margin: 0 !important;
                font-size: clamp(12px, 3.25vw, 14px) !important;
                line-height: 1.65 !important;
                color: #5f5f68 !important;
            }

            .product-detail-page .product-actions {
                gap: clamp(12px, 3vw, 18px) !important;
                width: 100% !important;
            }

            .product-detail-page .product-selections,
            .product-detail-page .attribute-section,
            .product-detail-page .quantity-section,
            .product-detail-page .share-section,
            .product-detail-page .delivery-check {
                min-width: 0 !important;
            }

            .product-detail-page .attribute-section {
                gap: 8px !important;
                margin: 0 !important;
                align-items: flex-start !important;
            }

            .product-detail-page .attribute-title,
            .product-detail-page .quantity-title,
            .product-detail-page .share-title,
            .product-detail-page .delivery-title {
                font-size: clamp(10px, 2.7vw, 12px) !important;
                letter-spacing: 0.08em !important;
                line-height: 1.45 !important;
            }

            .product-detail-page .attribute-title {
                display: flex !important;
                flex-wrap: wrap !important;
                align-items: baseline !important;
                gap: 4px 6px !important;
                text-transform: uppercase !important;
                color: #667085 !important;
            }

            .product-detail-page .attribute-title span {
                display: inline-flex !important;
                align-items: center !important;
                color: #1f2937 !important;
                font-weight: 800 !important;
                letter-spacing: 0.02em !important;
                text-transform: none !important;
            }

            .product-detail-page .swatch-container {
                display: flex !important;
                flex-wrap: wrap !important;
                gap: 8px !important;
                align-items: center !important;
                justify-content: flex-start !important;
                width: 100% !important;
                margin: 0 !important;
                padding-top: 2px !important;
            }

            .product-detail-page .color-swatch {
                width: clamp(30px, 8.5vw, 36px) !important;
                height: clamp(30px, 8.5vw, 36px) !important;
                flex: 0 0 auto !important;
                display: inline-flex !important;
            }

            .product-detail-page .size-btn {
                min-height: clamp(34px, 9vw, 38px) !important;
                padding: 6px 12px !important;
                font-size: clamp(11px, 2.9vw, 12px) !important;
                border-radius: 10px !important;
            }

            .product-detail-page .quantity-section {
                display: flex !important;
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 8px !important;
            }

            .product-detail-page .quantity-picker {
                display: inline-grid !important;
                grid-template-columns: 40px minmax(40px, auto) 40px !important;
                align-items: center !important;
                min-height: 42px !important;
                height: 42px !important;
                width: auto !important;
                border-radius: 12px !important;
                overflow: hidden !important;
                border: 1px solid #e5e7eb !important;
                background: #fff !important;
            }

            .product-detail-page .qty-btn,
            .product-detail-page .qty-input {
                width: 40px !important;
                height: 42px !important;
            }

            .product-detail-page .qty-input {
                font-size: clamp(13px, 3vw, 15px) !important;
            }

            .product-detail-page .product-actions-group {
                display: grid !important;
                grid-template-columns: minmax(0, 1fr) !important;
                gap: 6px !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .product-detail-page .btn-add-cart,
            .product-detail-page .btn-buy-now {
                width: 100% !important;
                min-height: clamp(46px, 12vw, 52px) !important;
                height: auto !important;
                padding: 0 16px !important;
                border-radius: 12px !important;
                font-size: clamp(11px, 3vw, 12.5px) !important;
                font-family: "Plus Jakarta Sans", "Outfit", "Instrument Sans", "Segoe UI", "Times New Roman", sans-serif !important;
                letter-spacing: 0.08em !important;
                justify-content: center !important;
            }

            .product-detail-page .share-section {
                display: flex !important;
                flex-wrap: wrap !important;
                align-items: center !important;
                gap: 10px !important;
                margin-top: 0 !important;
                padding-top: 12px !important;
            }

            .product-detail-page .share-links {
                flex-wrap: wrap;
                gap: 10px !important;
            }

            .product-detail-page .delivery-check {
                width: 100% !important;
                margin-top: 0 !important;
                padding: clamp(12px, 3.4vw, 16px) !important;
                border-radius: clamp(14px, 3vw, 18px) !important;
                background: linear-gradient(180deg, #fff 0%, var(--pdp-soft-bg) 100%) !important;
            }

            .product-detail-page .pincode-input-group {
                display: grid !important;
                grid-template-columns: minmax(0, 1fr) auto !important;
                gap: 8px !important;
            }

            .product-detail-page .pincode-input,
            .product-detail-page .btn-pincode {
                height: 42px !important;
                border-radius: 10px !important;
            }

            .product-detail-page .btn-pincode {
                min-width: 96px;
            }

            .product-detail-page .product-extra-info {
                width: 100% !important;
                max-width: 100% !important;
                margin-top: clamp(18px, 5vw, 28px) !important;
                padding: var(--pdp-card-padding) !important;
                border-radius: var(--pdp-card-radius) !important;
                background: linear-gradient(180deg, #ffffff 0%, #fffaf6 100%) !important;
                border: 1px solid var(--pdp-border) !important;
                box-shadow: 0 18px 40px rgba(15, 23, 42, 0.05) !important;
                overflow: clip !important;
                box-sizing: border-box !important;
            }

            .product-detail-page .tabs-info {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px !important;
                padding-bottom: 14px !important;
                border-bottom: 1px solid rgba(15, 23, 42, 0.08) !important;
            }

            .product-detail-page .tab-btn {
                width: 100% !important;
                min-width: 0 !important;
                min-height: 42px !important;
                padding: 10px 12px !important;
                border-radius: 999px !important;
                font-size: clamp(10px, 2.8vw, 12px) !important;
                line-height: 1.25 !important;
                letter-spacing: 0.08em !important;
                text-align: center !important;
                white-space: normal !important;
                color: #475467 !important;
                border: 1px solid rgba(169, 27, 67, 0.12) !important;
                background: linear-gradient(180deg, #ffffff 0%, #fff9f4 100%) !important;
                box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.6) !important;
            }

            .product-detail-page .tab-btn.active {
                color: #ffffff !important;
                background: linear-gradient(135deg, #b01748 0%, #cf3c69 100%) !important;
                border-color: transparent !important;
                box-shadow: 0 14px 28px rgba(169, 27, 67, 0.22) !important;
            }

            .product-detail-page .tab-btn.active::after {
                display: none !important;
            }

            .product-detail-page .tab-pane {
                padding-top: clamp(16px, 4vw, 24px) !important;
            }

            .product-detail-page .tab-content-text {
                max-width: min(100%, 72ch) !important;
                font-size: clamp(13px, 3.2vw, 14px) !important;
                line-height: 1.65 !important;
            }

            .product-detail-page .specs-table {
                width: 100% !important;
                max-width: 100% !important;
            }

            .product-detail-page .specs-label {
                width: 38% !important;
            }

            .product-detail-page .specs-table td {
                padding: 10px 12px !important;
                font-size: clamp(12px, 3vw, 13px) !important;
            }

            .product-detail-page .reviews-container,
            .product-detail-page .review-empty-state,
            .product-detail-page .review-inline-panel {
                max-width: 100% !important;
                min-width: 0 !important;
            }

            }

            @media (max-width: 359px) {
                .product-detail-page .product-detail-grid,
                .product-detail-page .product-extra-info {
                    padding: 12px !important;
                }

                .product-detail-page .tab-btn {
                    font-size: 10px !important;
                    padding-inline: 8px !important;
                }

                .product-detail-page .pincode-input-group {
                    grid-template-columns: minmax(0, 1fr) !important;
                }

                .product-detail-page .btn-pincode {
                    width: 100% !important;
                }
            }

            @media (min-width: 480px) and (max-width: 767px) {
                .product-detail-page .product-actions-group {
                    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                }
            }
        </style>
    @endpush
    <main class="product-detail-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp;
                <a href="{{ url('sarees') }}">Sarees</a> &nbsp; / &nbsp;
                <span>{{ $product->name }}</span>
            </div>

            <div class="product-detail-grid">
                <!-- Gallery Section -->
                <div class="product-gallery">
                    @php
                        $allImages = [];
                        // Main Images
                        if ($product->images && is_array($product->images) && count($product->images) > 0) {
                            foreach ($product->images as $img) {
                                $allImages[] = [
                                    'url' => asset(
                                        'uploads/' . (str_starts_with($img, 'products/') ? $img : 'products/' . $img),
                                    ),
                                    'color_id' => null,
                                ];
                            }
                        } elseif ($product->image_path) {
                            $path = $product->image_path;
                            $url = (Str::startsWith($path, 'products/') || Str::startsWith($path, 'categories/')) 
                                ? asset('uploads/' . $path) 
                                : asset('images/' . $path);
                            $allImages[] = [
                                'url' => $url,
                                'color_id' => null,
                            ];
                        }

                        // Color-specific Images (Legacy)
                        $colorImagesMap = $product->color_images ?? [];
                        foreach ($colorImagesMap as $colorId => $imgs) {
                            foreach ((array) $imgs as $img) {
                                $allImages[] = [
                                    'url' => asset(
                                        'uploads/' . (str_starts_with($img, 'products/') ? $img : 'products/' . $img),
                                    ),
                                    'color_id' => $colorId,
                                ];
                            }
                        }

                        // Variant-specific Images (Multiple)
                        if ($product->product_variants) {
                            foreach ($product->product_variants as $variant) {
                                $vImgs = is_array($variant->images)
                                    ? $variant->images
                                    : json_decode($variant->images ?? '[]', true) ?? [];
                                if (empty($vImgs) && $variant->image) {
                                    $vImgs = [$variant->image];
                                }

                                foreach ($vImgs as $vImg) {
                                    $allImages[] = [
                                        'url' => asset('uploads/' . $vImg),
                                        'color_id' => null,
                                        'variant_id' => $variant->id,
                                    ];
                                }
                            }
                        }

                        if (empty($allImages)) {
                            $allImages[] = ['url' => asset('images/pro.png'), 'color_id' => null];
                        }

                        $mainImage = $allImages[0]['url'];
                    @endphp
                    <div class="main-product-image" id="zoomContainer" style="position: relative;">
                        <img src="{{ $mainImage }}" alt="{{ $product->name }}" id="mainImg">
                        <button type="button" class="btn-wishlist-detail wishlist-btn" id="wishlistBtn"
                            aria-label="Add to Wishlist" data-product-id="{{ $product->id }}"
                            style="position: absolute; top: 15px; right: 15px; width: 42px; height: 42px; background: rgba(255,255,255,0.9); border: none; display: flex; align-items: center; justify-content: center; border-radius: 50%; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.1); z-index: 10;">
                            <i class="{{ $inWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart" id="wishlistIcon"
                                style="color: #A91B43; font-size: 18px;"></i>
                        </button>
                    </div>
                    <div class="product-thumbnails" id="thumbnailsContainer" style="display: none;">
                        @foreach ($allImages as $i => $imgData)
                            <div class="thumbnail {{ $i === 0 ? 'active' : '' }}" data-color-id="{{ $imgData['color_id'] }}"
                                data-variant-id="{{ $imgData['variant_id'] ?? '' }}"
                                onclick="changeImg('{{ $imgData['url'] }}', this)">
                                <img src="{{ $imgData['url'] }}" alt="View {{ $i + 1 }}">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Info Section -->
                <div class="product-info-details">
                    <div class="product-meta">
                        <p class="product-brand">{{ $product->category->name ?? 'Nandhini Silks Exclusive' }}</p>
                        <p class="product-meta-item">SKU: <span
                                class="product-sku">{{ strtoupper($product->sku) ?: 'NS-' . strtoupper(Str::slug($product->name)) }}</span>
                        </p>
                    </div>

                    <h1 class="product-title-detail">{{ $product->name }}</h1>

                    <div class="product-summary-row flex flex-wrap items-center gap-x-4 gap-y-1 mb-2">

                        <div class="product-rating">
                            <div class="stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= round($product->average_rating) ? 'fas' : 'far' }} fa-star"></i>
                                @endfor
                            </div>
                            <span>
                                {{ number_format($product->average_rating, 1) }}
                                @if ($product->reviews_count > 0)
                                    ({{ $product->reviews_count }})
                                @endif
                            </span>
                        </div>
                        <div class="product-price-section">
                            <span class="current-price" id="displayPrice">₹{{ number_format($product->price, 0) }}</span>
                            @if ($product->regular_price > $product->price)
                                <span class="old-price"
                                    id="displayRegularPrice">₹{{ number_format($product->regular_price, 0) }}</span>
                                <span class="discount-badge" id="displayDiscount">{{ $product->discount_percent }}%
                                    OFF</span>
                            @endif
                        </div>
                        <div class="stock-status">
                            @php
                                $totalVariantStock = $product->product_variants->sum('stock_quantity');
                                $isInStock =
                                    $product->product_variants->count() > 0
                                        ? $totalVariantStock > 0
                                        : $product->stock_quantity > 0;
                            @endphp
                            <span id="stockStatus" class="stock-badge {{ $isInStock ? 'stock-in' : 'stock-out' }}">
                                {{ $isInStock ? 'IN STOCK' : 'OUT OF STOCK' }}
                            </span>
                        </div>
                    </div>
                    <p class="product-tax-note">(Inclusive of all taxes)</p>

                    @if ($product->short_description)
                        <div class="product-description-short">
                            {!! Str::limit(strip_tags($product->short_description), 150) !!}
                        </div>
                    @endif

                    <form class="product-actions" method="POST" action="{{ route('cart.add', $product->id) }}"
                        id="pdpForm">
                        @csrf
                        <input type="hidden" name="quantity" id="qtyInput" value="1">

                        @if (!empty($attributeGroups))
                            <div class="product-selections" style="margin-bottom: 25px;">
                                @foreach ($attributeGroups as $group)
                                    @php
                                        $attrId = $group['attribute']->id;
                                        $attrName = $group['attribute']->name;
                                    @endphp
                                    <div class="attribute-section">
                                        <h3 class="attribute-title">
                                            {{ $attrName }}: <span id="label_{{ $attrId }}">Select
                                                {{ $attrName }}</span>
                                        </h3>
                                        <input type="hidden" name="attributes[{{ $attrId }}]"
                                            id="attr_{{ $attrId }}" value="">
                                        <div class="swatch-container">
                                            @foreach ($group['values'] as $value)
                                                @php
                                                    $isColorAttr =
                                                        strtolower($attrName) == 'color' ||
                                                        str_contains(strtolower($attrName), 'color');
                                                    $swatch = $value->swatch_value;
                                                    // Fallback to variant image for color swatch if swatch_value is missing
                                                    if (
                                                        !$swatch &&
                                                        $isColorAttr &&
                                                        isset($colorImagesMap[$value->id])
                                                    ) {
                                                        $swatch = $colorImagesMap[$value->id][0];
                                                    }

                                                    $isHexColor =
                                                        $swatch &&
                                                        preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $swatch);
                                                @endphp

                                                @php
                                                    // Determine if this particular VALUE can be rendered as a color swatch
                                                    $canRenderAsSwatch = false;
                                                    $bgStyle = '';
                                                    if ($isHexColor) {
                                                        $canRenderAsSwatch = true;
                                                        $bgStyle = "background: $swatch;";
                                                    } elseif ($swatch) {
                                                        $canRenderAsSwatch = true;
                                                        $bgStyle =
                                                            "background-image: url('" .
                                                            asset('uploads/' . $swatch) .
                                                            "'); background-size: cover; background-position: center;";
                                                    } elseif ($isColorAttr) {
                                                        // Only use color-name mapping for color attributes — but ONLY if the name is a recognized color
                                                        $colorMap = [
                                                            'gold' => '#D4AF37',
                                                            'mustard' => '#E1AD01',
                                                            'maroon' => '#800000',
                                                            'navy' => '#000080',
                                                            'navy blue' => '#000080',
                                                            'cream' => '#FFFDD0',
                                                            'white' => '#FFFFFF',
                                                            'black' => '#000000',
                                                            'red' => '#CC0000',
                                                            'pink' => '#FF69B4',
                                                            'blue' => '#0000CD',
                                                            'green' => '#006400',
                                                            'yellow' => '#FFD700',
                                                            'orange' => '#FF8C00',
                                                            'purple' => '#800080',
                                                            'grey' => '#808080',
                                                            'gray' => '#808080',
                                                            'brown' => '#8B4513',
                                                            'beige' => '#F5F5DC',
                                                            'bottle green' => '#006A4E',
                                                            'rama blue' => '#008080',
                                                            'pista green' => '#93C572',
                                                            'onion pink' => '#D192A0',
                                                            'copper' => '#B87333',
                                                            'silver' => '#C0C0C0',
                                                        ];
                                                        $colorName = strtolower($value->name);
                                                        if (isset($colorMap[$colorName])) {
                                                            $canRenderAsSwatch = true;
                                                            $bgStyle = 'background: ' . $colorMap[$colorName] . ';';
                                                        }
                                                    }
                                                @endphp

                                                @if ($canRenderAsSwatch)
                                                    <div class="attribute-option color-swatch"
                                                        data-attr-id="{{ $attrId }}"
                                                        data-value-id="{{ $value->id }}" onclick="selectAttribute(this)"
                                                        style="{{ $bgStyle }}" title="{{ $value->name }}"></div>
                                                @else
                                                    <button type="button" class="attribute-option size-btn"
                                                        data-attr-id="{{ $attrId }}"
                                                        data-value-id="{{ $value->id }}" onclick="selectAttribute(this)"
                                                        style="border: 1.5px solid #e0e0e0; background: #f5f5f5; color: #333; cursor: pointer; font-weight: 700; transition: all 0.15s ease;">
                                                        {{ $value->name }}
                                                    </button>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Quantity Selector -->
                        <div class="quantity-section">
                            <h3 class="quantity-title">Quantity</h3>
                            <div class="quantity-picker">
                                <button type="button" class="qty-btn" onclick="updateQty(-1)">−</button>
                                <input type="text" class="qty-input" value="1" readonly id="qtyDisp">
                                <button type="button" class="qty-btn" onclick="updateQty(1)">+</button>
                            </div>
                        </div>
                        <!-- Action Buttons -->
                        <div class="product-actions-group">
                            <button type="submit" name="action" value="cart" id="addToCartBtn"
                                class="btn-add-cart {{ !$isInStock ? 'disabled' : '' }}">
                                <i class="fas fa-shopping-bag"></i>
                                {{ $isInStock ? 'ADD TO CART' : 'OUT OF STOCK' }}
                            </button>
                            
                            <button type="submit" name="action" value="checkout" class="btn-buy-now"
                                {{ !$isInStock ? 'disabled' : '' }}>
                                <i class="fas fa-bolt"></i>
                                BUY IT NOW
                            </button>
                        </div>
                    </form>

                    <div class="share-section">
                        <span class="share-title">Share this product:</span>
                        <div class="share-links">
                            <!-- SVG Social Icons (Simplified) -->
                            <a href="#"><svg width="18" height="18" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" />
                                </svg></a>
                            <a href="#"><svg width="18" height="18" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.417-.923 3.9 3.9 0 0 0 .923-1.417c.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.999 0zm0 1.44c2.144 0 2.405.008 3.25.047.781.036 1.206.166 1.493.28.384.148.658.324.945.61.285.286.463.56.611.944.113.287.243.712.28 1.493.038.845.046 1.106.046 3.25s-.008 2.405-.047 3.25c-.036.781-.166 1.206-.28 1.493-.148.384-.324.658-.61.945-.287.285-.56.463-.945.611-.286.113-.712.243-1.493.28-.845.038-1.106.047-3.25.047s-2.405-.009-3.25-.047c-.781-.036-1.206-.166-1.493-.28a3.14 3.14 0 0 1-.945-.611 3.14 3.14 0 0 1-.61-.945c-.114-.287-.244-.712-.28-1.493-.039-.845-.047-1.106-.047-3.25s.008-2.405.047-3.25c.036-.781.166-1.206.28-1.493.148-.384.324-.658.61-.945.286-.287.561-.463.945-.611.287-.113.712-.243 1.493-.28C5.594 1.448 5.854 1.44 8 1.44z" />
                                    <path
                                        d="M8 3.86a4.14 4.14 0 1 0 0 8.28 4.14 4.14 0 0 0 0-8.28zm0 6.84a2.7 2.7 0 1 1 0-5.4 2.7 2.7 0 0 1 0 5.4zm4.316-6.685a.972.972 0 1 1-1.944 0 .972.972 0 0 1 1.944 0z" />
                                </svg></a>
                            <a href="#"><svg width="18" height="18" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.061 3.972L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z" />
                                </svg></a>
                        </div>
                    </div>

                    <div class="delivery-check">
                        <p class="delivery-title">Check Delivery Availability</p>
                        <div class="pincode-input-group">
                            <input type="text" class="pincode-input" placeholder="Enter Pincode">
                            <button class="btn-pincode">Check</button>
                        </div>
                        <p class="delivery-note">Free shipping on orders above ₹5,000.</p>
                    </div>
                </div>
            </div>

            <!-- Detail Tabs Sections -->
            <div class="product-extra-info">
                <div class="tabs-info">
                    <button class="tab-btn active" onclick="switchTab(event, 'tabDesc')">Full Description</button>
                    <button class="tab-btn" onclick="switchTab(event, 'tabSpecs')">Specifications</button>
                    <button class="tab-btn" onclick="switchTab(event, 'tabReviews')">Reviews</button>
                    <button class="tab-btn" onclick="switchTab(event, 'tabShipping')">Shipping & Returns</button>
                </div>

                <div class="tab-pane active" id="tabDesc">
                    <div class="tab-content-text">
                        {!! $product->short_description !!}
                    </div>
                </div>

                <div class="tab-pane" id="tabSpecs">
                    <div class="tab-content-text">
                        {!! $product->full_description !!}
                    </div>
                </div>

                <div class="tab-pane" id="tabReviews">
                    <div class="reviews-container">
                        <div class="review-entry-state {{ $errors->has('stars') || $errors->has('review') ? 'hidden' : '' }}"
                            id="reviewEntryState">
                            @if ($product->reviews()->count() > 0)
                                <div class="space-y-6">
                                    @foreach ($product->reviews as $review)
                                        <div class="review-item border-b border-gray-100 pb-6">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="text-[#FFB800] text-sm">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="{{ $i <= $review->stars ? 'fas' : 'far' }} fa-star"></i>
                                                    @endfor
                                                </div>
                                                <span
                                                    class="font-bold text-sm text-gray-800">{{ $review->user->name ?? 'User' }}</span>
                                                <span
                                                    class="text-xs text-gray-400">{{ $review->created_at->format('M d, Y') }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600 leading-relaxed">{{ $review->review }}</p>
                                        </div>
                                    @endforeach
                                </div>
                                @auth
                                    <button type="button" class="review-write-btn" id="openReviewFormBtn"
                                        style="margin-top: 24px;">
                                        <i class="far fa-pen-to-square"></i>
                                        {{ $userReview ? 'Update Your Review' : 'Write a Review' }}
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="review-write-btn"
                                        style="margin-top: 24px; text-decoration: none;">
                                        <i class="far fa-pen-to-square"></i>
                                        Write a Review
                                    </a>
                                @endauth
                            @else
                                <div class="review-empty-state">
                                    <div class="review-empty-icon">
                                        <i class="far fa-comments"></i>
                                    </div>
                                    <h4 class="review-empty-title">No reviews yet</h4>
                                    <p class="review-empty-text">Be the first to share your thoughts about this product.
                                    </p>
                                    @auth
                                        <button type="button" class="review-write-btn" id="openReviewFormBtn">
                                            <i class="far fa-pen-to-square"></i>
                                            Write a Review
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}" class="review-write-btn"
                                            style="text-decoration: none;">
                                            <i class="far fa-pen-to-square"></i>
                                            Write a Review
                                        </a>
                                    @endauth
                                </div>
                            @endif
                        </div>

                        @auth
                            <div class="review-inline-panel {{ $errors->has('stars') || $errors->has('review') ? 'open' : '' }}"
                                id="reviewInlinePanel">
                                <div class="review-modal-header">
                                    <h3 class="review-modal-title">{{ $userReview ? 'Update Your Review' : 'Write a Review' }}
                                    </h3>
                                    <button type="button" class="review-modal-close" id="closeReviewFormBtn"
                                        aria-label="Close review form">&times;</button>
                                </div>
                                <form method="POST" action="{{ route('product.review.store', $product) }}">
                                    @csrf
                                    <div class="review-form-group">
                                        <label class="review-form-label" for="reviewStars">Your Rating</label>
                                        <select class="review-form-select" name="stars" id="reviewStars" required>
                                            <option value="">Select rating</option>
                                            @for ($i = 5; $i >= 1; $i--)
                                                <option value="{{ $i }}"
                                                    {{ old('stars', $userReview->stars ?? '') == $i ? 'selected' : '' }}>
                                                    {{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="review-form-group">
                                        <label class="review-form-label" for="reviewText">Your Review</label>
                                        <textarea class="review-form-textarea" name="review" id="reviewText" required minlength="10"
                                            placeholder="Share your experience with this product...">{{ old('review', $userReview->review ?? '') }}</textarea>
                                    </div>
                                    <div class="review-form-actions">
                                        <button type="button" class="review-form-cancel"
                                            id="cancelReviewFormBtn">Cancel</button>
                                        <button type="submit"
                                            class="review-form-submit">{{ $userReview ? 'Update Review' : 'Submit Review' }}</button>
                                    </div>
                                </form>
                            </div>
                        @endauth
                    </div>
                </div>

                <div class="tab-pane" id="tabShipping">
                    <div class="tab-content-text shipping-info">
                        <h4>Domestic Shipping (India)</h4>
                        <ul>
                            <li>Standard Delivery: 5-7 business days.</li>
                            <li>Express Delivery: 2-3 business days.</li>
                        </ul>
                        <h4 class="mt-4">Return Policy</h4>
                        <p>Easy 7-day hassle-free returns. Tags must be intact.</p>
                    </div>
                </div>
            </div>

            <!-- Related Products Section -->
            @if (isset($relatedProducts) && $relatedProducts->count() > 0)
                <section class="related-products">
                    <h2 class="related-title">Related Collections</h2>
                    <div class="swiper-wrap-outer">
                        <div class="swiper related-swiper">
                            <div class="swiper-wrapper">
                                @foreach ($relatedProducts->concat($relatedProducts) as $related)
                                    <div class="swiper-slide">
                                        <article class="product-card-v2" style="height: 100%;">
                                            <a href="{{ route('product.show', $related->slug) }}"
                                                style="text-decoration: none; color: inherit;">
                                                <div class="product-image-v2">
                                                    @php
                                                        $relatedImage = 'images/pro.png';
                                                        if ($related->image_path) {
                                                            if (Str::startsWith($related->image_path, 'products/') || Str::startsWith($related->image_path, 'categories/')) {
                                                                $relatedImage = 'uploads/' . $related->image_path;
                                                            } else {
                                                                $relatedImage = 'images/' . $related->image_path;
                                                            }
                                                        } elseif (!empty($related->images)) {
                                                            $rImages = is_string($related->images) ? json_decode($related->images, true) : $related->images;
                                                            if (is_array($rImages) && count($rImages) > 0) {
                                                                $relatedImage = 'uploads/' . $rImages[0];
                                                            }
                                                        }
                                                    @endphp
                                                    <img src="{{ asset($relatedImage) }}" alt="{{ $related->name }}">
                                                </div>
                                                <div class="product-info-v2">
                                                    <h3 class="product-name-v2">{{ $related->name }}</h3>
                                                    <p class="product-price-v2">₹{{ number_format($related->price, 0) }}
                                                    </p>
                                                </div>
                                            </a>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- Navigation outside swiper container -->
                        <div class="swiper-button-next related-next"></div>
                        <div class="swiper-button-prev related-prev"></div>
                    </div>
                </section>
            @endif

            {{-- Recently Viewed Section --}}
            @if (isset($recentlyViewed) && count($recentlyViewed) > 0)
                <section class="recently-viewed" style="margin-top: 64px; margin-bottom: 64px;">
                    <h2 style="font-size: 24px; font-weight: 800; color: #1a1a1a; margin-bottom: 32px;">
                        Recently Viewed
                        <div style="width: 40px; height: 3px; background: #a91b43; margin-top: 8px;"></div>
                    </h2>

                    <div class="product-grid"
                        style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px;">
                        @foreach ($recentlyViewed as $recent)
                            @include('frontend.partials.product-card', ['product' => $recent])
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </main>

    </div>
@endsection

@push('scripts')
    <script>
        function changeImg(src, thumb) {
            document.getElementById('mainImg').src = src;
            const thumbs = document.querySelectorAll('.thumbnail');
            thumbs.forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
        }

        function updateQty(val) {
            const input = document.getElementById('qtyDisp');
            const hiddenInput = document.getElementById('qtyInput');
            const maxQuantity = 10;
            let current = parseInt(input.value);
            let next = current + val;
            if (next < 1) next = 1;
            if (next > maxQuantity) next = maxQuantity;

            if (next === current) return;

            input.value = next;
            if (hiddenInput) hiddenInput.value = next;

            // If we are in "GO TO CART" state, update the cart immediately
            const btn = document.getElementById('addToCartBtn');
            if (btn && btn.classList.contains('go-to-cart-state')) {
                // Determine what to send to 'cart.add'
                // Since 'cart.add' ADDS to current quantity, we send 'val' (1 or -1)
                // BUT only if we can find the variant context.
                // We'll call the existing AJAX handler but with the specific increment
                syncCartQuantity(val);
            }
        }

        function syncCartQuantity(increment) {
            const form = document.getElementById('pdpForm');
            const formData = new FormData(form);
            formData.set('quantity', increment); // Only add/subtract 1
            formData.append('action', 'cart');

            fetch(form.getAttribute('action'), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message || 'Cart updated.');
                        if (window.updateMiniCart) window.updateMiniCart();
                        if (window.notifyCartUpdate) window.notifyCartUpdate();
                        
                        // Update local tracking
                        updateLocalCartQuantity(increment);
                    } else {
                        toastr.error(data.message || 'Error updating cart.');
                        // Revert local UI if failed? 
                        // For now we just stay as is.
                        location.reload(); // Safer to sync from server
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Something went wrong.');
                });
        }

        function updateLocalCartQuantity(increment) {
            // Find current matched variant and update cartVariantQuantities
            let selectedAttrs = [];
            document.querySelectorAll('input[id^="attr_"]').forEach(input => {
                if (input.value) selectedAttrs.push(parseInt(input.value));
            });
            let matched = productVariants.find(v => {
                if (!v.combination) return false;
                let vValues = Object.values(v.combination).flat().map(Number);
                return selectedAttrs.length === vValues.length && selectedAttrs.every(id => vValues.includes(id));
            });

            if (matched) {
                cartVariantQuantities[matched.id] = (cartVariantQuantities[matched.id] || 0) + increment;
            } else {
                cartVariantQuantities['base'] = (cartVariantQuantities['base'] || 0) + increment;
            }
        }

        const productVariants = {!! json_encode($product->product_variants) !!};
        const basePrice = {{ $product->price }};
        const baseRegularPrice = {{ $product->regular_price ?: $product->price }};
        const baseSku = "{{ $product->sku }}";
        let cartVariantIds = {!! json_encode($cartVariantIds ?? []) !!};
        let cartVariantQuantities = {!! json_encode($cartVariantQuantities ?? []) !!};
        const initialProductInCart = @json($inCart);

        // Global Sync Listener for this product
        window.syncCartStateWithServer = function() {
            fetch('{{ url("cart/mini-cart") }}', {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                // Reset local tracking
                cartVariantIds = [];
                cartVariantQuantities = {};
                
                // Re-populate from mini-cart data for THIS product
                const thisProductId = {{ $product->id }};
                data.items.forEach(item => {
                    if (item.product_id == thisProductId) {
                        if (item.variant_id) {
                            cartVariantIds.push(item.variant_id);
                            cartVariantQuantities[item.variant_id] = item.quantity;
                        } else {
                            cartVariantQuantities['base'] = item.quantity;
                        }
                    }
                });
                // Trigger UI update
                checkVariant();
            });
        };

        function selectAttribute(element) {
            const attrId = element.getAttribute('data-attr-id');
            const valueId = element.getAttribute('data-value-id');

            // Remove active from peers
            const container = element.parentElement;
            container.querySelectorAll('.attribute-option').forEach(opt => opt.classList.remove('active'));

            // Add active to current
            element.classList.add('active');

            // Update hidden input
            const input = document.getElementById('attr_' + attrId);
            if (input) input.value = valueId;

            // Update label
            const label = document.getElementById('label_' + attrId);
            if (label) {
                const title = element.getAttribute('title') || element.innerText.trim();
                label.innerText = title;
            }

            // Update Gallery if Color
            if (element.classList.contains('color-swatch')) {
                updateGallery(valueId);
            }

            // Sync Variants and Availability
            updateOptionsAvailability();
            checkVariant();
        }

        function updateOptionsAvailability() {
            const allSections = document.querySelectorAll('.attribute-section');
            const selectedAttrs = {};

            // Collect currently selected attributes
            document.querySelectorAll('input[id^="attr_"]').forEach(input => {
                const attrId = input.id.replace('attr_', '');
                if (input.value) selectedAttrs[attrId] = parseInt(input.value);
            });

            allSections.forEach(section => {
                const sectionAttrId = section.querySelector('input[id^="attr_"]').id.replace('attr_', '');
                const options = section.querySelectorAll('.attribute-option');

                options.forEach(opt => {
                    const valueId = parseInt(opt.getAttribute('data-value-id'));

                    // Assume we selected this option, can we find ANY variant that matches it 
                    // COMBINED with other currently selected attributes?
                    let testSelection = {
                        ...selectedAttrs
                    };
                    testSelection[sectionAttrId] = valueId;

                    let isAvailable = productVariants.some(v => {
                        if (!v.combination) return false;

                        // Check if this variant matches ALL our testSelection criteria
                        let matches = Object.entries(testSelection).every(([aid, vid]) => {
                            if (!v.combination[aid]) return false;
                            return v.combination[aid].includes(vid);
                        });

                        // It's ONLY available if it exists AND has stock > 0
                        return matches && v.stock_quantity > 0;
                    });

                    if (isAvailable) {
                        opt.style.opacity = '1';
                        opt.style.pointerEvents = 'auto';
                        opt.classList.remove('unavailable');
                    } else {
                        opt.style.opacity = '0.3';
                        opt.style.pointerEvents = 'none'; // Optional: disable clicking
                        opt.classList.add('unavailable');
                        // If it's the currently active one but now unavailable, mark it
                        if (opt.classList.contains('active')) {
                            // opt.classList.remove('active');
                        }
                    }
                });
            });
        }

        function updatePriceDisplay(sale, regular) {
            document.getElementById('displayPrice').innerText = '₹' + new Intl.NumberFormat().format(sale);
            const regEl = document.getElementById('displayRegularPrice');
            const discEl = document.getElementById('displayDiscount');

            if (regEl && regular > sale) {
                regEl.innerText = '₹' + new Intl.NumberFormat().format(regular);
                regEl.style.display = 'inline';
                if (discEl) {
                    let pct = Math.round(((regular - sale) / regular) * 100);
                    if (pct > 0) {
                        discEl.innerText = pct + '% OFF';
                        discEl.style.display = 'inline';
                    } else {
                        discEl.style.display = 'none';
                    }
                }
            } else if (regEl) {
                regEl.style.display = 'none';
                if (discEl) discEl.style.display = 'none';
            }
        }

        function checkVariant() {
            let selectedAttrs = [];
            document.querySelectorAll('input[id^="attr_"]').forEach(input => {
                if (input.value) selectedAttrs.push(parseInt(input.value));
            });

            // Match against v.combination (e.g. { "1": [10], "2": [15] })
            let matched = productVariants.find(v => {
                if (!v.combination) return false;
                let vValues = Object.values(v.combination).flat().map(Number);
                return selectedAttrs.length === vValues.length && selectedAttrs.every(id => vValues.includes(id));
            });

            const btn = document.getElementById('addToCartBtn');
            const isInStock = matched ? matched.stock_quantity > 0 : {{ $product->stock_quantity > 0 ? 'true' : 'false' }};

            if (matched) {
                // If variant has only one price column, assume it's the 'Sale price' 
                // and compare with Main Regular Price or its own if available
                let vSale = parseFloat(matched.sale_price || matched.price || basePrice);
                let vRegular = parseFloat(matched.price && matched.sale_price && matched.price > matched.sale_price ?
                    matched.price : baseRegularPrice);

                updatePriceDisplay(vSale, vRegular);
                document.querySelector('.product-sku').innerText = matched.sku || baseSku;
                updateStockStatus(matched.stock_quantity);

                // Swap Main Image and filter thumbnails for this variant
                updateGallery(null, matched.id);

                // Handle Add to Cart vs Go to Cart state
                if (btn) {
                    if (cartVariantIds.includes(matched.id) || cartVariantIds.includes(matched.id.toString())) {
                        setGoToCartState(btn);
                        // Sync Quantity Picker
                        const qtyDisp = document.getElementById('qtyDisp');
                        const cartQty = cartVariantQuantities[matched.id] || cartVariantQuantities[matched.id.toString()] || 1;
                        if (qtyDisp) qtyDisp.value = cartQty;
                    } else {
                        resetAddToCartState(btn, isInStock);
                        // Default to 1 for new selection
                        const qtyDisp = document.getElementById('qtyDisp');
                        if (qtyDisp) qtyDisp.value = 1;
                    }
                }
            } else {
                updatePriceDisplay(basePrice, baseRegularPrice);
                document.querySelector('.product-sku').innerText = baseSku;
                updateStockStatus({{ $product->stock_quantity }});
                updateGallery(null, null); // Show general images
                
                if (btn) {
                    // Check if the base product (no variant) is in cart
                    if (initialProductInCart && (cartVariantIds.length === 0 || cartVariantQuantities['base'])) {
                        setGoToCartState(btn);
                        const qtyDisp = document.getElementById('qtyDisp');
                        if (qtyDisp) qtyDisp.value = cartVariantQuantities['base'] || 1;
                    } else {
                        resetAddToCartState(btn, {{ $product->stock_quantity > 0 ? 'true' : 'false' }});
                        const qtyDisp = document.getElementById('qtyDisp');
                        if (qtyDisp) qtyDisp.value = 1;
                    }
                }
            }
        }

        function setGoToCartState(btn) {
            btn.innerHTML = '<i class="fas fa-shopping-cart"></i> GO TO CART';
            btn.classList.add('go-to-cart-state');
            btn.style.background = '#2a2a2a';
            btn.type = 'button';
            btn.onclick = function() { window.location.href = "{{ route('cart') }}"; };
        }

        function resetAddToCartState(btn, isInStock) {
            btn.innerHTML = '<i class="fas fa-shopping-bag"></i> ' + (isInStock ? 'ADD TO CART' : 'OUT OF STOCK');
            btn.classList.remove('go-to-cart-state');
            btn.style.background = ''; // Revert to CSS default
            btn.type = 'submit';
            btn.onclick = null;
            if (!isInStock) {
                btn.classList.add('disabled');
            } else {
                btn.classList.remove('disabled');
            }
        }

        window.onload = function() {
            // Auto-select first option for each attribute section
            document.querySelectorAll('.attribute-section').forEach(section => {
                const firstOption = section.querySelector('.attribute-option:not(.unavailable)');
                if (firstOption) {
                    selectAttribute(firstOption);
                }
            });
            // Initial check
            checkVariant();
        };

        function updateStockStatus(qty) {
            const el = document.getElementById('stockStatus');
            if (qty > 0) {
                el.innerText = 'IN STOCK';
                el.className = 'stock-badge stock-in';
                el.style.color = '#2ecc71';
                el.style.background = '#f0fff4';
                el.style.borderColor = '#c6f6d5';
            } else {
                el.innerText = 'OUT OF STOCK';
                el.className = 'stock-badge stock-out';
                el.style.color = '#e74c3c';
                el.style.background = '#fff5f5';
                el.style.borderColor = '#fed7d7';
            }
        }

        function updateGallery(colorId, variantId) {
            const thumbs = document.querySelectorAll('.thumbnail');
            let firstFound = null;
            let countVisible = 0;

            // First pass: try to find exact matches
            thumbs.forEach(t => {
                const thumbColorId = t.getAttribute('data-color-id');
                const thumbVariantId = t.getAttribute('data-variant-id');

                let show = false;
                if (variantId && thumbVariantId == variantId) {
                    show = true;
                } else if (!variantId && colorId && (thumbColorId == colorId)) {
                    show = true;
                }

                if (show) {
                    t.style.display = 'block';
                    if (!firstFound) firstFound = t;
                    countVisible++;
                } else {
                    t.style.display = 'none';
                }
            });

            // Fallback: If no specific images found, show general images
            if (countVisible === 0) {
                thumbs.forEach(t => {
                    const thumbColorId = t.getAttribute('data-color-id');
                    const thumbVariantId = t.getAttribute('data-variant-id');

                    // Show images that aren't tied to a specific color or variant
                    if ((!thumbColorId || thumbColorId === 'null') && !thumbVariantId) {
                        t.style.display = 'block';
                        if (!firstFound) firstFound = t;
                        countVisible++;
                    }
                });
            }

            if (firstFound) {
                const img = firstFound.querySelector('img');
                if (img) changeImg(img.src, firstFound);
            }
        }

        function switchTab(e, tabId) {
            const btns = document.querySelectorAll('.tab-btn');
            btns.forEach(b => b.classList.remove('active'));
            e.currentTarget.classList.add('active');
            const panes = document.querySelectorAll('.tab-pane');
            panes.forEach(p => p.style.display = 'none');
            document.getElementById(tabId).style.display = 'block';
        }

        // Initialize Swipers
        document.addEventListener('DOMContentLoaded', function() {
            const reviewEntryState = document.getElementById('reviewEntryState');
            const reviewInlinePanel = document.getElementById('reviewInlinePanel');
            const openReviewFormBtn = document.getElementById('openReviewFormBtn');
            const closeReviewFormBtn = document.getElementById('closeReviewFormBtn');
            const cancelReviewFormBtn = document.getElementById('cancelReviewFormBtn');

            const openReviewForm = () => {
                if (reviewInlinePanel) {
                    reviewInlinePanel.classList.add('open');
                    if (reviewEntryState) {
                        reviewEntryState.classList.add('hidden');
                    }
                    reviewInlinePanel.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
            };

            const closeReviewForm = () => {
                if (reviewInlinePanel) {
                    reviewInlinePanel.classList.remove('open');
                    if (reviewEntryState) {
                        reviewEntryState.classList.remove('hidden');
                    }
                }
            };

            if (openReviewFormBtn) {
                openReviewFormBtn.addEventListener('click', openReviewForm);
            }

            if (closeReviewFormBtn) {
                closeReviewFormBtn.addEventListener('click', closeReviewForm);
            }

            if (cancelReviewFormBtn) {
                cancelReviewFormBtn.addEventListener('click', closeReviewForm);
            }

            // Auto Select First Options
            let hasSelection = false;
            document.querySelectorAll('.swatch-container').forEach(container => {
                const firstOpt = container.querySelector('.attribute-option');
                if (firstOpt) {
                    firstOpt.click();
                    hasSelection = true;
                }
            });

            if (!hasSelection) {
                // If simple product without attributes, just load base images
                updateGallery(null, null);
            }

            // Unhide the thumbnails container after JS filters it
            const tc = document.getElementById('thumbnailsContainer');
            if (tc) tc.style.display = 'flex';

            const swiperOptions = {
                slidesPerView: 2,
                spaceBetween: 20,
                loop: true,
                observer: true,
                observeParents: true,
                watchOverflow: true,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                },
                navigation: {
                    nextEl: '.related-next',
                    prevEl: '.related-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2
                    },
                    768: {
                        slidesPerView: 3
                    },
                    1024: {
                        slidesPerView: 4
                    },
                }
            };

            if (document.querySelector('.related-swiper')) {
                new Swiper('.related-swiper', swiperOptions);
            }
            if (document.querySelector('.recently-swiper')) {
                new Swiper('.recently-swiper', {
                    ...swiperOptions,
                    navigation: {
                        nextEl: '.recently-next',
                        prevEl: '.recently-prev',
                    }
                });
            }

        });
        // AJAX Add to Cart
        document.getElementById('pdpForm').addEventListener('submit', function(e) {
            const action = e.submitter ? e.submitter.value : 'cart';

            if (action === 'cart') {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append('action', 'cart');

                fetch(this.getAttribute('action'), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.message || 'Added to cart.');
                            if (window.updateMiniCart) window.updateMiniCart();
                            
                            // Re-fetch or locally update cartVariantIds if needed, 
                            // but for simplicity we can check the current matched variant
                            let selectedAttrs = [];
                            document.querySelectorAll('input[id^="attr_"]').forEach(input => {
                                if (input.value) selectedAttrs.push(parseInt(input.value));
                            });
                            let matched = productVariants.find(v => {
                                if (!v.combination) return false;
                                let vValues = Object.values(v.combination).flat().map(Number);
                                return selectedAttrs.length === vValues.length && selectedAttrs.every(id => vValues.includes(id));
                            });

                            if (matched && !cartVariantIds.includes(matched.id)) {
                                cartVariantIds.push(matched.id);
                                // Update quantity too
                                const qtyEntered = parseInt(document.getElementById('qtyDisp').value) || 1;
                                cartVariantQuantities[matched.id] = qtyEntered;
                            } else if (!matched) {
                                // Base product
                                const qtyEntered = parseInt(document.getElementById('qtyDisp').value) || 1;
                                cartVariantQuantities['base'] = (cartVariantQuantities['base'] || 0) + qtyEntered;
                            }
                            
                            checkVariant();
                            if (window.notifyCartUpdate) window.notifyCartUpdate();
                        } else {
                            toastr.error(data.message || 'Error adding to cart.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('Something went wrong.');
                    });
            }
        });
    </script>
@endpush
