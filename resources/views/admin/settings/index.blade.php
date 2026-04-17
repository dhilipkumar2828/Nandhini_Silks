@extends('admin.layouts.admin')

@section('title', 'Mail & System Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-glass rounded-3xl overflow-hidden">
        <div class="gradient-bg p-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-3xl font-black tracking-tight">System Configuration</h2>
                <p class="text-white/80 font-medium mt-1">Manage your email and system preferences dynamically</p>
            </div>
            <i class="fas fa-cog absolute -right-8 -bottom-8 text-9xl text-white/10 rotate-12"></i>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="p-8 space-y-8">
            @csrf
            
            <!-- Mail Configuration Section -->
            <div class="space-y-6">
                <div class="flex items-center space-x-3 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-[#a91b43]">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-800 tracking-tight">SMTP Mail Configuration</h3>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Outgoing Mail Server Details</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-[13px] font-black text-slate-700 ml-1">Mail Mailer</label>
                        <input type="text" name="mail_mailer" value="{{ $settings['mail_mailer'] ?? 'smtp' }}" 
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/10 transition-all outline-none font-medium placeholder:text-slate-300" 
                            placeholder="e.g. smtp">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[13px] font-black text-slate-700 ml-1">Mail Host</label>
                        <input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? 'mail.nandhinisilks.com' }}" 
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/10 transition-all outline-none font-medium placeholder:text-slate-300" 
                            placeholder="e.g. mail.nandhinisilks.com">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[13px] font-black text-slate-700 ml-1">Mail Port</label>
                        <input type="text" name="mail_port" value="{{ $settings['mail_port'] ?? '465' }}" 
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/10 transition-all outline-none font-medium placeholder:text-slate-300" 
                            placeholder="e.g. 465 or 587">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[13px] font-black text-slate-700 ml-1">Mail Encryption</label>
                        <select name="mail_encryption" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/10 transition-all outline-none font-medium">
                            <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="tls" {{ ($settings['mail_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="null" {{ ($settings['mail_encryption'] ?? '') == 'null' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[13px] font-black text-slate-700 ml-1">Mail Username</label>
                        <input type="text" name="mail_username" value="{{ $settings['mail_username'] ?? 'noreply@nandhinisilks.com' }}" 
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/10 transition-all outline-none font-medium placeholder:text-slate-300" 
                            placeholder="Email address">
                    </div>

                    <div class="space-y-1.5" x-data="{ show: false }">
                        <label class="text-[13px] font-black text-slate-700 ml-1">Mail Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="mail_password" value="{{ $settings['mail_password'] ?? '' }}" 
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/10 transition-all outline-none font-medium placeholder:text-slate-300" 
                                placeholder="Email password">
                            <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#a91b43] transition-colors">
                                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[13px] font-black text-slate-700 ml-1">Mail From Address</label>
                        <input type="email" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? 'noreply@nandhinisilks.com' }}" 
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/10 transition-all outline-none font-medium placeholder:text-slate-300" 
                            placeholder="From email address">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[13px] font-black text-slate-700 ml-1">Mail From Name</label>
                        <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? 'Nandhini Silks' }}" 
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/10 transition-all outline-none font-medium placeholder:text-slate-300" 
                            placeholder="Sender name">
                    </div>
                </div>
            </div>

            <!-- Environment & Routing Section -->
            <div class="space-y-6 pt-6 border-t border-slate-100">
                <div class="flex items-center space-x-3 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-100">
                        <i class="fas fa-server"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-800 tracking-tight">System Environment & Routing</h3>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Toggle between local testing and live production</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[13px] font-black text-slate-700 ml-1">Current Active Environment</label>
                        <div class="flex p-1.5 bg-slate-100 rounded-2xl w-fit">
                            <label class="cursor-pointer">
                                <input type="radio" name="mail_mode" value="local" {{ ($settings['mail_mode'] ?? 'local') == 'local' ? 'checked' : '' }} class="sr-only peer">
                                <div class="px-6 py-2 rounded-xl text-xs font-black transition-all peer-checked:bg-white peer-checked:text-[#a91b43] peer-checked:shadow-sm text-slate-400">
                                    <i class="fas fa-laptop-code mr-1.5"></i> LOCAL
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="mail_mode" value="live" {{ ($settings['mail_mode'] ?? 'local') == 'live' ? 'checked' : '' }} class="sr-only peer">
                                <div class="px-6 py-2 rounded-xl text-xs font-black transition-all peer-checked:bg-[#a91b43] peer-checked:text-white peer-checked:shadow-lg text-slate-400">
                                    <i class="fas fa-rocket mr-1.5"></i> LIVE
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[13px] font-black text-slate-700 ml-1">Local Recipient Email <span class="text-[10px] bg-slate-100 px-2 py-0.5 rounded ml-1 text-slate-400">Used when LOCAL is active</span></label>
                            <input type="email" name="local_notification_email" value="{{ $settings['local_notification_email'] ?? 'local-test@example.com' }}" 
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/10 transition-all outline-none font-medium text-slate-600">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[13px] font-black text-slate-700 ml-1">Live Recipient Email <span class="text-[10px] bg-emerald-50 px-2 py-0.5 rounded ml-1 text-emerald-600 uppercase">Live Production</span></label>
                            <input type="email" name="live_notification_email" value="{{ $settings['live_notification_email'] ?? 'orders@nandhinisilks.com' }}" 
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/10 transition-all outline-none font-medium text-slate-800">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Notifications Section -->
            <div class="space-y-6 pt-6 border-t border-slate-100">
                <div class="flex items-center space-x-3 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-800 tracking-tight">Admin & Support Notifications</h3>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Global Fallback Emails</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-[13px] font-black text-slate-700 ml-1">Default Order Email (Fallback)</label>
                        <input type="email" name="order_notification_email" value="{{ $settings['order_notification_email'] ?? 'orders@nandhinisilks.com' }}" 
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/10 transition-all outline-none font-medium placeholder:text-slate-300" 
                            placeholder="orders@nandhinisilks.com">
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-[13px] font-black text-slate-700 ml-1">System Support Email</label>
                        <input type="email" name="support_email" value="{{ $settings['support_email'] ?? 'support@nandhinisilks.com' }}" 
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/10 transition-all outline-none font-medium placeholder:text-slate-300" 
                            placeholder="support@nandhinisilks.com">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4">
                <button type="reset" class="px-6 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-50 transition-all">
                    Reset Changes
                </button>
                <button type="submit" class="gradient-bg px-10 py-3 rounded-xl font-bold text-white shadow-lg shadow-rose-500/20 hover:shadow-rose-500/40 hover:-translate-y-0.5 transition-all">
                    Save System Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
