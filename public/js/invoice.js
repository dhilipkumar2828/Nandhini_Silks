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
        const date = new Date(dateString);
        return date.toLocaleDateString('en-GB').replace(/\//g, '-');
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
        const {
            orderNumber = "NS-2026-88421",
            date = new Date(),
            customer = {
                name: "Raswanth Sabarish",
                address: "416/9 Aranmanai Street, S.V. Nagaram, Arni, Tamil Nadu - 632317",
                phone: "+91 96295 52822"
            },
            items = [
                { name: "Royal Gold Handloom Silk Saree", variant: "Gold Jari", hsn: "5007", qty: 1, rate: 7490, taxRate: 12, discount: 0 }
            ],
            paymentMethod = "Razorpay",
            subtotal = 7490,
            taxAmount = 898,
            shipping = 0,
            total = 8388
        } = data;

        const dateStr = InvoiceGenerator.formatDate(date);
        const amountInWords = InvoiceGenerator.getAmountInWords(total);

        // Simple tax breakdown for demonstration
        const cgst = (taxAmount / 2).toFixed(2);
        const sgst = (taxAmount / 2).toFixed(2);

        return `
            <div id="invoice-template-container" style="padding: 0; margin: 0; background: #fff;">
                <div id="invoice-template" style="width: 210mm; min-height: 297mm; padding: 15mm; font-family: 'Plus Jakarta Sans', Arial, sans-serif; background: white; color: #333; box-sizing: border-box; line-height: 1.5; font-size: 13px; position: relative;">
                    
                    <!-- Header -->
                    <div style="display: flex; justify-content: space-between; align-items: start; border-bottom: 2px solid #a91b43; padding-bottom: 20px; margin-bottom: 25px;">
                        <div>
                            <img src="images/image 1.png" alt="Nandhini Silks" style="height: 60px; margin-bottom: 10px;">
                            <div style="font-weight: 700; font-size: 20px; color: #a91b43; letter-spacing: 0.5px;">NANDHINI SILKS</div>
                            <div style="font-size: 11px; color: #666; line-height: 1.4;">
                                416/9 Aranmanai Street, S.V. Nagaram<br>
                                Arni - 632317, Tamil Nadu, India<br>
                                <strong>GSTIN:</strong> 33AAAAA0000A1Z5 | <strong>Ph:</strong> +91 96295 52822
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <h1 style="margin: 0; font-size: 26px; color: #a91b43; text-transform: uppercase; font-weight: 800;">Invoice</h1>
                            <div style="margin-top: 15px; font-size: 12px; line-height: 1.6;">
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <span style="color: #999;">Invoice No:</span> <strong style="color: #333;">${orderNumber}</strong>
                                </div>
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <span style="color: #999;">Date:</span> <strong style="color: #333;">${dateStr}</strong>
                                </div>
                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <span style="color: #999;">Payment:</span> <strong style="color: #333;">${paymentMethod}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer & Details Grid -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                        <div style="padding: 15px; background: #fffcf0; border-radius: 12px; border: 1px solid #f9e1e8;">
                            <div style="font-weight: 700; text-transform: uppercase; font-size: 10px; color: #a91b43; margin-bottom: 8px; letter-spacing: 1px;">Customer Details</div>
                            <strong style="font-size: 14px; color: #333;">${customer.name}</strong><br>
                            <div style="font-size: 12px; color: #555; margin-top: 5px; line-height: 1.4;">
                                ${customer.address.split(',').join('<br>')}
                            </div>
                            <div style="margin-top: 5px; font-size: 12px;"><strong>Phone:</strong> ${customer.phone}</div>
                        </div>
                        <div style="padding: 15px; border: 1px solid #eee; border-radius: 12px; display: flex; flex-direction: column; justify-content: center;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span style="color: #999;">Place of Supply:</span>
                                <strong>Tamil Nadu</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #999;">Order Type:</span>
                                <strong>Prepaid</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table (Formal Tabular Format) -->
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #eee; overflow: hidden; border-radius: 8px;">
                        <thead>
                            <tr style="background: #a91b43; color: white;">
                                <th style="padding: 10px; text-align: left; border: 1px solid #a91b43; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">SNo</th>
                                <th style="padding: 10px; text-align: center; border: 1px solid #a91b43; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Image</th>
                                <th style="padding: 10px; text-align: left; border: 1px solid #a91b43; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Items Description</th>
                                <th style="padding: 10px; text-align: center; border: 1px solid #a91b43; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Qty</th>
                                <th style="padding: 10px; text-align: right; border: 1px solid #a91b43; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Rate</th>
                                <th style="padding: 10px; text-align: right; border: 1px solid #a91b43; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${items.map((item, i) => `
                                <tr style="background: ${i % 2 === 0 ? '#fff' : '#fafafa'};">
                                    <td style="padding: 10px; border: 1px solid #eee; text-align: center;">${i + 1}</td>
                                    <td style="padding: 10px; border: 1px solid #eee; text-align: center;">
                                        <img src="${item.image || 'images/pro1.png'}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #eee;">
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #eee;">
                                        <strong style="color: #333;">${item.name}</strong><br>
                                        <span style="font-size: 10px; color: #888;">${item.variant}</span>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #eee; text-align: center; font-weight: 600;">${item.qty}</td>
                                    <td style="padding: 10px; border: 1px solid #eee; text-align: right;">₹${InvoiceGenerator.formatCurrency(item.rate)}</td>
                                    <td style="padding: 10px; border: 1px solid #eee; text-align: right; font-weight: 700; color: #333;">₹${InvoiceGenerator.formatCurrency(item.qty * item.rate)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>

                    <!-- Totals & Tax Table -->
                    <div style="display: flex; justify-content: flex-end; margin-bottom: 30px;">
                        <table style="width: 320px; border-collapse: collapse; border: 1px solid #eee;">
                            <tr>
                                <td style="padding: 10px; border: 1px solid #eee; font-weight: 600; color: #666; background: #fafafa;">Subtotal</td>
                                <td style="padding: 10px; border: 1px solid #eee; text-align: right; font-weight: 600;">₹${InvoiceGenerator.formatCurrency(subtotal)}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #eee; font-weight: 600; color: #666; background: #fafafa;">GST (CGST 6% + SGST 6%)</td>
                                <td style="padding: 10px; border: 1px solid #eee; text-align: right; font-weight: 600;">₹${InvoiceGenerator.formatCurrency(taxAmount)}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px; border: 1px solid #eee; font-weight: 600; color: #666; background: #fafafa;">Shipping</td>
                                <td style="padding: 10px; border: 1px solid #eee; text-align: right; font-weight: 600; color: ${shipping === 0 ? '#2e7d32' : '#333'}">${shipping === 0 ? "FREE" : "₹" + InvoiceGenerator.formatCurrency(shipping)}</td>
                            </tr>
                            <tr style="background: #a91b43; color: white;">
                                <td style="padding: 12px; border: 1px solid #a91b43; font-weight: 700; font-size: 16px;">Net Total</td>
                                <td style="padding: 12px; border: 1px solid #a91b43; text-align: right; font-weight: 700; font-size: 16px;">₹${InvoiceGenerator.formatCurrency(total)}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Words & Signature -->
                    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 40px; align-items: end; border-top: 1px solid #eee; padding-top: 20px;">
                        <div>
                            <div style="margin-bottom: 20px;">
                                <span style="font-weight: 700; text-transform: uppercase; font-size: 10px; color: #999; letter-spacing: 1px;">Amount in Words</span>
                                <div style="font-weight: 700; color: #333; margin-top: 5px; font-size: 14px;">INR ${amountInWords}</div>
                            </div>
                            
                            <div style="font-size: 11px; color: #888; border-left: 2px solid #a91b43; padding-left: 10px;">
                                <strong>Declaration:</strong><br>
                                This is a computer generated invoice. No signature is required. We declare that this invoice shows the actual price of the goods described.
                            </div>
                        </div>

                        <div style="text-align: right;">
                            <div style="margin-bottom: 10px; font-weight: 700; color: #333; text-transform: uppercase; font-size: 11px;">For NANDHINI SILKS</div>
                            <div style="height: 60px; display: flex; align-items: center; justify-content: flex-end;">
                                <img src="images/image 1.png" style="height: 40px; opacity: 0.2; filter: grayscale(1);">
                            </div>
                            <div style="border-top: 1px solid #ccc; display: inline-block; width: 180px; padding-top: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #a91b43;">Authorized Signatory</div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div style="position: absolute; bottom: 15mm; left: 15mm; right: 15mm; text-align: center; font-size: 10px; color: #aaa; border-top: 1px dotted #ddd; padding-top: 15px;">
                        Nandhini Silks - Excellence in Saree Handlooms. Subject to Local Jurisdiction.
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
        container.style.position = 'fixed'; // Use fixed to ensure it's in viewport
        container.style.left = '-10000px';
        container.style.top = '0';
        container.style.width = '210mm'; // Standard A4 Width
        container.innerHTML = html;
        document.body.appendChild(container);

        // Options for html2pdf
        const opt = {
            margin:       0,
            filename:     `Invoice_${orderData.orderNumber || 'NS'}.pdf`,
            image:        { type: 'jpeg', quality: 1.0 },
            html2canvas:  { 
                scale: 2, 
                useCORS: true,
                logging: false,
                letterRendering: true,
                allowTaint: true
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

            html2pdf()
                .set(opt)
                .from(element)
                .save()
                .then(() => {
                    // Cleanup after save completes
                    document.body.removeChild(container);
                })
                .catch(err => {
                    console.error('Invoice Generation Error:', err);
                    document.body.removeChild(container);
                    alert('Error generating invoice. Please try again.');
                });
        }, 500); // 500ms delay for safety
    }
};
