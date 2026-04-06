/**
 * Invoice Generator Utility for Nandhini Silks
 */

const InvoiceGenerator = {
    /**
     * Helper to format currency (Indian Format)
     */
    formatCurrency: (amount) => {
        return parseFloat(amount || 0).toLocaleString('en-IN', {
            maximumFractionDigits: 2,
            minimumFractionDigits: 2
        });
    },

    /**
     * Helper to format date
     */
    formatDate: (dateString) => {
        if (!dateString) return new Date().toLocaleDateString('en-GB').replace(/\//g, '-');
        return dateString.replace(/\//g, '-');
    },

    /**
     * Convert amount to words (Indian System)
     */
    getAmountInWords: (num) => {
        const ones = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine"];
        const tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
        const teens = ["Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];

        function convert(n) {
            if (n === 0) return "";
            if (n < 10) return ones[n];
            if (n < 20) return teens[n - 10];
            if (n < 100) return tens[Math.floor(n / 10)] + (n % 10 !== 0 ? " " + ones[n % 10] : "");
            return ones[Math.floor(n / 100)] + " Hundred" + (n % 100 !== 0 ? " and " + convert(n % 100) : "");
        }

        if (num === 0) return "Zero Rupees Only";
        let n = Math.floor(num);
        let result = "";
        
        if (Math.floor(n / 10000000) > 0) { result += convert(Math.floor(n / 10000000)) + " Crore "; n %= 10000000; }
        if (Math.floor(n / 100000) > 0) { result += convert(Math.floor(n / 100000)) + " Lakh "; n %= 100000; }
        if (Math.floor(n / 1000) > 0) { result += convert(Math.floor(n / 1000)) + " Thousand "; n %= 1000; }
        if (n > 0) { result += convert(n); }
        
        return result.trim() + " Rupees Only";
    },

    /**
     * Generate HTML for the invoice
     */
    generateHTML: (data) => {
        const origin = window.location.origin;
        const {
            orderNumber = "",
            date = new Date(),
            customer = {
                name: "",
                address: "",
                phone: ""
            },
            items = [],
            paymentMethod = "",
            subtotal = 0,
            discount = 0,
            taxAmount = 0,
            shipping = 0,
            total = 0
        } = data;

        const dateStr = InvoiceGenerator.formatDate(date);
        const amountInWords = InvoiceGenerator.getAmountInWords(total);

        return `
            <div id="invoice-template-container" style="padding: 0; margin: 0; background: #fff;">
                <div id="invoice-template" style="width: 210mm; padding: 0 15mm; font-family: 'Outfit', 'Plus Jakarta Sans', Arial, sans-serif; background: white; color: #333; box-sizing: border-box; line-height: 1.5; font-size: 16px;">
                    
                    <!-- Header -->
                    <div style="display: flex; justify-content: space-between; align-items: start; border-bottom: 2px solid #a91b43; padding-bottom: 20px; margin-bottom: 25px;">
                        <div>
                            <img src="${origin}/images/image 1.png" alt="Nandhini Silks" style="height: 60px; margin-bottom: 10px;">
                            <div style="font-weight: 800; font-size: 32px; color: #a91b43; letter-spacing: 0.5px;">NANDHINI SILKS</div>
                            <div style="font-size: 16px; color: #666; line-height: 1.5;">
                                416/9 Aranmanai Street, S.V. Nagaram<br>
                                Arni - 632317, Tamil Nadu, India<br>
                                <strong>GSTIN:</strong> 33AAAAA0000A1Z5 | <strong>Ph:</strong> +91 96295 52822
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <h1 style="margin: 0; font-size: 40px; color: #a91b43; text-transform: uppercase; font-weight: 800;">Tax Invoice</h1>
                            <div style="margin-top: 15px; font-size: 18px; line-height: 1.6;">
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <span style="color: #999;">Invoice No:</span> <strong style="color: #333;">${orderNumber}</strong>
                                </div>
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <span style="color: #999;">Date:</span> <strong style="color: #333;">${dateStr}</strong>
                                </div>
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <span style="color: #999;">Payment Status:</span> <strong style="color: #333;">${paymentMethod}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer & Details Grid -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                        <div style="padding: 15px; background: #fffcf0; border-radius: 12px; border: 1px solid #f9e1e8;">
                            <div style="font-weight: 700; text-transform: uppercase; font-size: 14px; color: #a91b43; margin-bottom: 8px; letter-spacing: 1px;">Billing & Shipping Details</div>
                            <strong style="font-size: 22px; color: #333; display: block; margin-bottom: 4px;">${customer.name}</strong>
                            <div style="font-size: 16px; color: #555; line-height: 1.5;">
                                ${customer.address}
                            </div>
                            <div style="margin-top: 8px; font-size: 16px;"><strong>Phone:</strong> ${customer.phone}</div>
                        </div>
                        <div style="padding: 15px; border: 1px solid #eee; border-radius: 12px; display: flex; flex-direction: column; justify-content: center; background: #fafafa;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 16px;">
                                <span style="color: #777;">Place of Supply:</span>
                                <strong style="color: #333;">Tamil Nadu</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 16px;">
                                <span style="color: #777;">Order Source:</span>
                                <strong style="color: #333;">nandhinisilks.com</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #eee; overflow: hidden; border-radius: 8px; font-size: 16px;">
                        <thead>
                            <tr style="background: #a91b43; color: white;">
                                <th style="padding: 12px; text-align: center; border: 1px solid #a91b43;">SNo</th>
                                <th style="padding: 12px; text-align: center; border: 1px solid #a91b43;">Preview</th>
                                <th style="padding: 12px; text-align: left; border: 1px solid #a91b43;">Item Description</th>
                                <th style="padding: 12px; text-align: center; border: 1px solid #a91b43;">Qty</th>
                                <th style="padding: 12px; text-align: right; border: 1px solid #a91b43;">Rate</th>
                                <th style="padding: 12px; text-align: right; border: 1px solid #a91b43;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${items.map((item, i) => `
                                <tr style="background: ${i % 2 === 0 ? '#fff' : '#fafafa'}; transition: background 0.2s; page-break-inside: avoid;">
                                    <td style="padding: 12px; border: 1px solid #eee; text-align: center; color: #777;">${i + 1}</td>
                                    <td style="padding: 12px; border: 1px solid #eee; text-align: center;">
                                        <img src="${item.image || origin + '/images/product_detail.png'}" style="width: 45px; height: 55px; object-fit: cover; border-radius: 6px; border: 1px solid #f0f0f0;">
                                    </td>
                                    <td style="padding: 12px; border: 1px solid #eee;">
                                        <strong style="color: #1a1a1a; font-size: 18px;">${item.name}</strong><br>
                                        <div style="font-size: 14px; color: #999; margin-top: 4px;">HSN: ${item.hsn} | Variant: ${item.variant}</div>
                                    </td>
                                    <td style="padding: 12px; border: 1px solid #eee; text-align: center; font-weight: 600;">${item.qty}</td>
                                    <td style="padding: 12px; border: 1px solid #eee; text-align: right;">₹${InvoiceGenerator.formatCurrency(item.rate)}</td>
                                    <td style="padding: 12px; border: 1px solid #eee; text-align: right; font-weight: 700; color: #1a1a1a;">₹${InvoiceGenerator.formatCurrency(item.qty * item.rate)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>

                    <!-- Totals & Tax Table -->
                    <div style="display: flex; justify-content: flex-end; margin-bottom: 30px; page-break-inside: avoid;">
                        <table style="width: 320px; border-collapse: collapse; border: 1px solid #eee; background: #fff;">
                            <tr>
                                <td style="padding: 10px; border: 1px solid #eee; color: #666;">Subtotal</td>
                                <td style="padding: 10px; border: 1px solid #eee; text-align: right; font-weight: 600;">₹${InvoiceGenerator.formatCurrency(subtotal)}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #eee; color: #666;">Tax (GST Included)</td>
                                <td style="padding: 10px; border: 1px solid #eee; text-align: right; font-weight: 600;">₹${InvoiceGenerator.formatCurrency(taxAmount)}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #eee; color: #666;">Shipping Fees</td>
                                <td style="padding: 10px; border: 1px solid #eee; text-align: right; font-weight: 600; color: ${shipping === 0 ? '#2e7d32' : '#1a1a1a'}">${shipping === 0 ? "FREE" : "₹" + InvoiceGenerator.formatCurrency(shipping)}</td>
                            </tr>
                            ${discount > 0 ? `
                            <tr style="background: #f1fcf1;">
                                <td style="padding: 10px; border: 1px solid #eee; color: #2e7d32; font-weight: 600;">Discount</td>
                                <td style="padding: 10px; border: 1px solid #eee; text-align: right; font-weight: 700; color: #2e7d32;">- ₹${InvoiceGenerator.formatCurrency(discount)}</td>
                            </tr>
                            ` : ''}
                            <tr style="background: #a91b43; color: white;">
                                <td style="padding: 15px 12px; border: 1px solid #a91b43; font-weight: 800; font-size: 24px;">Net Total</td>
                                <td style="padding: 15px 12px; border: 1px solid #a91b43; text-align: right; font-weight: 800; font-size: 24px;">₹${InvoiceGenerator.formatCurrency(total)}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Words & Signature -->
                    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 40px; align-items: end; border-top: 1.5px solid #f0f0f0; padding-top: 25px; page-break-inside: avoid;">
                        <div>
                            <div style="margin-bottom: 25px;">
                                <span style="font-weight: 800; text-transform: uppercase; font-size: 14px; color: #bbb; letter-spacing: 1.2px; display: block; margin-bottom: 5px;">Amount in Words</span>
                                <div style="font-weight: 700; color: #A91B43; font-size: 18px; background: #fffcf0; padding: 8px 12px; border-radius: 6px; display: inline-block;">${amountInWords}</div>
                            </div>
                            
                            <div style="font-size: 14px; color: #999; border-left: 3px solid #a91b43; padding-left: 15px; font-style: italic; line-height: 1.5;">
                                <strong>Legal Declaration:</strong><br>
                                This digital receipt serves as an official tax invoice. Certified that the particulars given above are true and correct and the amount indicated represents the price actually charged. Computer generated - No signature required.
                            </div>
                        </div>

                        <div style="text-align: right;">
                            <div style="margin-bottom: 12px; font-weight: 800; color: #1a1a1a; text-transform: uppercase; font-size: 14px; letter-spacing: 0.5px;">For NANDHINI SILKS</div>
                            <div style="height: 70px; display: flex; align-items: center; justify-content: flex-end;">
                                <img src="${origin}/images/image 1.png" style="height: 50px; opacity: 0.15; filter: grayscale(1);">
                            </div>
                            <div style="border-top: 1.5px solid #333; display: inline-block; width: 200px; padding-top: 10px; font-size: 14px; font-weight: 800; color: #a91b43; text-transform: uppercase;">Authorized Signatory</div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div style="margin-top: 50px; text-align: center; font-size: 14px; color: #bbb; border-top: 1px solid #f0f0f0; padding-top: 15px; padding-bottom: 20px; page-break-inside: avoid;">
                        Thank you for choosing Nandhini Silks - Arani's Pride in Handloom Artistry since years.<br>
                        Visit us online at <strong>www.nandhinisilks.com</strong>
                    </div>
                </div>
            </div>
        `;
    },


    /**
     * Trigger PDF Download
     */
    download: (orderData) => {
        // Build the HTML content
        const html = InvoiceGenerator.generateHTML(orderData);
        
        // Create a temporary container
        const container = document.createElement('div');
        container.id = 'invoice-temp-container';
        container.style.position = 'absolute';
        container.style.left = '0';
        container.style.top = '0';
        container.style.width = '210mm';
        container.style.zIndex = '-9999';
        container.style.background = '#fff';
        container.innerHTML = html;
        document.body.appendChild(container);

        // Options for html2pdf
        const opt = {
            margin:       [15, 0, 15, 0],
            pagebreak:    { mode: ['css', 'legacy'] },
            filename:     `Invoice_${orderData.orderNumber || 'NS'}.pdf`,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { 
                scale: 2, 
                useCORS: true,
                logging: false,
                letterRendering: true,
                allowTaint: false,
                scrollY: 0,
                windowScrolling: false
            },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Small delay to ensure any internal elements or styles are fully parsed
        setTimeout(() => {
            const element = container.querySelector('#invoice-template');
            if (!element) {
                console.error('Invoice template element not found');
                document.body.removeChild(container);
                return;
            }

            // Temporarily scroll to top to avoid displacement issues in some browsers
            const oldScrollY = window.scrollY;
            window.scrollTo(0, 0);

            html2pdf()
                .set(opt)
                .from(element)
                .save()
                .then(() => {
                    // Restore scroll and cleanup
                    window.scrollTo(0, oldScrollY);
                    document.body.removeChild(container);
                })
                .catch(err => {
                    console.error('Invoice Generation Error:', err);
                    window.scrollTo(0, oldScrollY);
                    if (document.getElementById('invoice-temp-container')) {
                        document.body.removeChild(container);
                    }
                    alert('Error generating invoice. Please try again.');
                });
        }, 300);
    }
};

