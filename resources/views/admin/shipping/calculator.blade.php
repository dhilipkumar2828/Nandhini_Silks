@extends('admin.layouts.admin')

@section('content')
<div class="p-6 space-y-8" 
     x-data="{ 
        activeTab: 'calculator',
        loading: false,
        results: null,
        error: null,
        
        async submitCalculator(e) {
            this.loading = true;
            this.results = null;
            this.error = null;
            try {
                const formData = new FormData(e.target);
                const res = await fetch('{{ route('admin.shipping.calculator.check') }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await res.json();
                if (data.success) {
                    this.results = { type: 'rates', data: data.data };
                } else {
                    this.error = data.message;
                }
            } catch (err) {
                this.error = 'Connection failed.';
            } finally {
                this.loading = false;
            }
        },

        async submitTracker(e) {
            this.loading = true;
            this.results = null;
            this.error = null;
            try {
                const formData = new FormData(e.target);
                const res = await fetch('{{ route('admin.shipping.calculator.track') }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await res.json();
                console.log('DEBUG Tracking:', data); // CRITICAL FOR DEBUGGING
                
                if (data.success && data.data) {
                    // Normalize the data structure
                    const trackingData = data.data.tracking_data || data.data;
                    this.results = { type: 'tracking', data: trackingData };
                } else {
                    this.error = 'Tracking data not found or invalid AWB.';
                }
            } catch (err) {
                this.error = 'Tracking bridge offline.';
            } finally {
                this.loading = false;
            }
        }
     }">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 text-slate-900">
        <div>
            <h1 class="text-4xl font-black tracking-tight">Logistics Intelligence</h1>
            <p class="text-slate-500 font-medium">Powering your supply chain with real-time Shiprocket insights.</p>
        </div>
        
        <!-- Tab Switcher -->
        <div class="bg-slate-100 p-1.5 rounded-2xl flex gap-2 border border-slate-200">
            <button @click="activeTab = 'calculator'; results = null; error = null;" 
                    :class="activeTab === 'calculator' ? 'bg-white shadow-xl text-slate-900' : 'text-slate-500 hover:text-slate-700'"
                    class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2">
                <i class="fas fa-calculator text-[10px]"></i> Rate Calculator
            </button>
            <button @click="activeTab = 'tracker'; results = null; error = null;" 
                    :class="activeTab === 'tracker' ? 'bg-white shadow-xl text-slate-900' : 'text-slate-500 hover:text-slate-700'"
                    class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2">
                <i class="fas fa-search-location text-[10px]"></i> Live Tracker
            </button>
        </div>
    </div>

    <!-- MAIN CONTENT GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <!-- LEFT PANEL: CONTROLS -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- CALCULATOR FORM -->
            <div x-show="activeTab === 'calculator'" class="card-glass p-8 rounded-[2rem] border-2 border-white shadow-2xl shadow-slate-200">
                <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-2">
                    <span class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                    Rate Parameters
                </h2>
                
                <form @submit.prevent="submitCalculator" class="space-y-6 no-loader">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 tracking-tighter ml-1">Delivery Pincode</label>
                        <div class="relative">
                            <i class="fas fa-map-marker-alt absolute left-4 top-1/2 -translate-y-1/2 text-rose-500"></i>
                            <input type="text" name="pincode" maxlength="6" placeholder="Type 6-digit pincode..." required
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl pl-12 pr-4 py-4 text-sm font-bold text-slate-800 outline-none focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 tracking-tighter ml-1">Dead Weight (KG)</label>
                        <div class="relative">
                            <i class="fas fa-weight-hanging absolute left-4 top-1/2 -translate-y-1/2 text-emerald-500"></i>
                            <input type="number" name="weight" step="0.1" value="0.5" min="0.1" required
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl pl-12 pr-4 py-4 text-sm font-bold text-slate-800 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 tracking-tighter ml-1">Transaction Mode</label>
                        <select name="cod" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-4 text-sm font-bold text-slate-800 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all cursor-pointer shadow-sm">
                            <option value="1">Cash on Delivery (COD)</option>
                            <option value="0">Prepaid (Online Payment)</option>
                        </select>
                    </div>

                    <button type="submit" :disabled="loading" class="w-full bg-slate-900 text-white py-5 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-black hover:shadow-2xl hover:shadow-slate-300 transition-all transform active:scale-[0.98] flex items-center justify-center gap-3 disabled:opacity-50">
                        <i class="fas fa-bolt" x-show="!loading"></i>
                        <i class="fas fa-circle-notch animate-spin" x-show="loading"></i>
                        <span x-text="loading ? 'Processing...' : 'Fetch Freight Rates'"></span>
                    </button>
                </form>
            </div>

            <!-- TRACKER FORM -->
            <div x-show="activeTab === 'tracker'" class="card-glass p-8 rounded-[2rem] border-2 border-white shadow-2xl shadow-slate-200">
                <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-2">
                    <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span>
                    Live Tracking Gateway
                </h2>
                
                <form @submit.prevent="submitTracker" class="space-y-6 no-loader">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-500 tracking-tighter ml-1">Tracking Number / AWB</label>
                        <div class="relative">
                            <i class="fas fa-barcode absolute left-4 top-1/2 -translate-y-1/2 text-indigo-500"></i>
                            <input type="text" name="awb" placeholder="Enter AWB Code (e.g. 123456...)" required
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl pl-12 pr-4 py-4 text-sm font-bold text-slate-800 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all shadow-sm">
                        </div>
                    </div>

                    <button type="submit" :disabled="loading" class="w-full bg-indigo-600 text-white py-5 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-indigo-700 hover:shadow-2xl hover:shadow-indigo-300 transition-all transform active:scale-[0.98] flex items-center justify-center gap-3 disabled:opacity-50">
                        <i class="fas fa-radar" x-show="!loading"></i>
                        <i class="fas fa-circle-notch animate-spin" x-show="loading"></i>
                        <span x-text="loading ? 'Tracing Order...' : 'Trace Shipment'"></span>
                    </button>
                </form>
            </div>

            <!-- SHIPROCKET LOGO CARD -->
            <div class="bg-indigo-900 rounded-[2rem] p-8 text-white relative overflow-hidden group">
                <div class="relative z-10">
                    <img src="https://www.shiprocket.in/wp-content/uploads/2023/01/shiprocket-logo-white.svg" alt="Shiprocket" class="h-6 mb-4 filter drop-shadow-md">
                    <p class="text-[11px] text-indigo-200 font-bold uppercase tracking-widest leading-relaxed">Official Logistics Integration Layer v2.0</p>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: RESULTS -->
        <div class="lg:col-span-2">
            
            <!-- INITIAL STATE -->
            <div x-show="!loading && !results && !error" class="h-full min-h-[500px] flex flex-col items-center justify-center bg-slate-50 border-4 border-dashed border-slate-100 rounded-[3rem] p-12 text-center">
                <i class="fas fa-satellite text-6xl text-slate-200 mb-8"></i>
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">System Ready for Scanning</h3>
                <p class="text-slate-500 text-sm max-w-sm mx-auto mt-2 font-medium">Provide a Pincode or AWB Number to begin real-time data streaming.</p>
            </div>

            <!-- LOADING STATE -->
            <div x-show="loading" class="h-full min-h-[500px] flex flex-col items-center justify-center bg-white rounded-[3rem] p-12">
                <div class="w-24 h-24 mb-10 relative">
                    <div class="absolute inset-0 border-8 border-slate-50 rounded-full"></div>
                    <div class="absolute inset-0 border-8 border-rose-500 rounded-full border-t-transparent animate-spin"></div>
                </div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Connecting to Shiprocket...</h3>
            </div>

            <!-- ERROR STATE -->
            <div x-show="error" class="h-full min-h-[500px] flex flex-col items-center justify-center bg-rose-50 rounded-[3rem] p-12 border-2 border-rose-100">
                <i class="fas fa-exclamation-triangle text-5xl text-rose-500 mb-6"></i>
                <h3 class="text-xl font-bold text-rose-900" x-text="error"></h3>
                <button @click="error = null" class="mt-6 text-xs font-black uppercase tracking-widest text-rose-500 underline">Dismiss</button>
            </div>

            <!-- RESULTS: RATES -->
            <div x-show="results && results.type === 'rates'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <template x-for="courier in results?.data || []">
                    <div class="card-glass p-8 rounded-[2rem] border-2 border-white shadow-xl hover:border-slate-900 transition-all group relative">
                        <div class="flex justify-between items-start mb-8">
                            <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-truck-moving text-xl text-slate-400"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Rate</p>
                                <p class="text-3xl font-black text-slate-900">₹<span x-text="Math.round(courier.rate)"></span></p>
                            </div>
                        </div>
                        <h4 class="text-lg font-black text-slate-900 mb-2 uppercase" x-text="courier.courier_name"></h4>
                        <span class="text-[10px] font-black px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full uppercase" x-text="'EDD: ' + (courier.etd || '3-5 Days')"></span>
                    </div>
                </template>
            </div>

            <!-- RESULTS: TRACKING -->
            <div x-show="results && results.type === 'tracking'" class="space-y-8">
                <!-- Header Info -->
                <div class="card-glass bg-slate-900 p-10 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                        <div>
                            <p class="text-[10px] font-black text-indigo-300 uppercase tracking-[0.2em] mb-3">Live Shipment Status</p>
                            <h2 class="text-5xl font-black uppercase tracking-tighter" 
                                :class="['delivered', 'shipped'].includes(results?.data?.shipment_track?.[0]?.current_status?.toLowerCase()) ? 'text-emerald-400' : 'text-indigo-400'"
                                x-text="results?.data?.shipment_track?.[0]?.current_status || 'SCANNING...'"></h2>
                            <div class="flex items-center gap-3 mt-4">
                                <i class="fas fa-history text-xs text-slate-500"></i>
                                <p class="text-[11px] text-slate-400 font-medium" x-text="'Updated at: ' + (results?.data?.shipment_track?.[0]?.current_timestamp || 'N/A')"></p>
                            </div>
                        </div>
                        <div class="md:text-right border-t md:border-t-0 md:border-l border-white/10 pt-6 md:pt-0 md:pl-8 flex flex-col justify-center">
                            <p class="text-[10px] font-black text-indigo-300 uppercase tracking-[0.2em] mb-2">Assigned Courier Partner</p>
                            <div class="flex md:justify-end items-center gap-3">
                                <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-shipping-fast text-lg"></i>
                                </div>
                                <p class="text-2xl font-black tracking-tight" x-text="results?.data?.shipment_track?.[0]?.courier_name || 'Calculating...'"></p>
                            </div>
                            <p class="text-xs text-slate-500 mt-2 font-bold" x-text="'AWB: ' + (results?.data?.shipment_track?.[0]?.awb_code || 'N/A')"></p>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="card-glass p-10 rounded-[2.5rem] border border-slate-100 shadow-xl bg-white">
                    <h3 class="text-xl font-black text-slate-900 mb-10 tracking-tight flex items-center gap-3">
                        <i class="fas fa-route text-indigo-500"></i> Full Transit Log
                    </h3>
                    <div class="space-y-12 relative before:content-[''] before:absolute before:left-3.5 before:top-2 before:bottom-2 before:w-1 before:bg-slate-50">
                        <template x-for="(act, idx) in results?.data?.shipment_track_activities || []">
                            <div class="relative pl-14">
                                <div class="absolute left-0 top-1 w-8 h-8 rounded-full border-4 border-white flex items-center justify-center z-10 shadow-sm"
                                     :class="idx === 0 ? 'bg-indigo-600 scale-110 shadow-indigo-200' : 'bg-slate-200'">
                                     <div x-show="idx === 0" class="w-2 h-2 bg-white rounded-full animate-ping"></div>
                                </div>
                                <div class="flex flex-col md:flex-row md:justify-between gap-2 mb-2">
                                    <h5 class="text-sm font-black text-slate-900 uppercase tracking-tight" x-text="act.activity"></h5>
                                    <span class="text-[10px] font-black text-slate-400 bg-slate-50 px-3 py-1 rounded-lg border border-slate-100" x-text="act.date"></span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium leading-relaxed" x-text="act.location"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Guidance for Admin -->
                <div class="bg-indigo-50/50 p-8 rounded-[2rem] border border-indigo-100">
                    <h4 class="text-[10px] font-black text-indigo-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i class="fas fa-map-signs"></i> Logistics Status Roadmap
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="space-y-1">
                            <p class="text-[9px] font-black text-indigo-400 uppercase">Step 1</p>
                            <p class="text-xs font-bold text-slate-900">NEW / PLACED</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[9px] font-black text-indigo-400 uppercase">Step 2</p>
                            <p class="text-xs font-bold text-slate-900">SHIP / PICKUP</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[9px] font-black text-indigo-400 uppercase">Step 3</p>
                            <p class="text-xs font-bold text-slate-900">IN TRANSIT</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[9px] font-black text-indigo-400 uppercase">Step 4</p>
                            <p class="text-xs font-bold text-slate-900">DELIVERED</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
// --- PINCODE HELPER ---
document.getElementById('pincodeInput')?.addEventListener('input', async function(e) {
    const pincode = e.target.value;
    const detailsDiv = document.getElementById('pincodeDetails');
    const areaSpan = document.getElementById('areaName');
    
    if (pincode.length === 6) {
        areaSpan.innerText = 'Consulting Registry...';
        detailsDiv.classList.remove('hidden');
        try {
            const response = await fetch(`https://api.postalpincode.in/pincode/${pincode}`);
            const data = await response.json();
            if (data[0].Status === "Success") {
                const po = data[0].PostOffice[0];
                areaSpan.innerText = `${po.Name}, ${po.District}, ${po.State}`;
                areaSpan.className = 'text-slate-700 font-bold';
            } else {
                areaSpan.innerText = 'Unknown Pincode';
                areaSpan.className = 'text-rose-500 font-bold';
            }
        } catch (error) { areaSpan.innerText = 'Service Busy'; }
    } else { detailsDiv.classList.add('hidden'); }
});
</script>
@endpush
