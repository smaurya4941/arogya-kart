<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-gray-900 dark:text-white leading-tight">
            {{ __('Subscription Management') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Current Plan -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-8 border border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Current Subscription</h3>
                @if($currentSubscription)
                    <div class="flex justify-between items-center bg-green-50 dark:bg-green-900/20 p-6 rounded-xl border border-green-100 dark:border-green-800">
                        <div>
                            <p class="text-sm text-green-600 dark:text-green-400 font-bold uppercase tracking-wide">Active Plan</p>
                            <h4 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $currentSubscription->plan->name }} ({{ ucfirst($currentSubscription->billing_cycle) }})</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Valid until: {{ $currentSubscription->ends_at ? \Carbon\Carbon::parse($currentSubscription->ends_at)->format('d M, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                Active
                            </span>
                        </div>
                    </div>
                @else
                    <div class="bg-red-50 dark:bg-red-900/20 p-6 rounded-xl border border-red-100 dark:border-red-800">
                        <p class="text-red-600 dark:text-red-400 font-bold">You do not have an active subscription.</p>
                        <p class="text-sm text-red-500 dark:text-red-300 mt-1">Please select a plan below to continue using the software.</p>
                    </div>
                @endif
            </div>

            <!-- Available Plans -->
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Available Plans</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($plans as $plan)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border {{ $currentSubscription && $currentSubscription->plan_id == $plan->id ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-100 dark:border-gray-700' }} overflow-hidden relative transition hover:shadow-lg">
                        @if($plan->name == 'Professional')
                            <div class="absolute top-0 right-0 bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg uppercase tracking-wide">Most Popular</div>
                        @endif
                        <div class="p-8">
                            <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $plan->name }}</h4>
                            <p class="text-gray-500 dark:text-gray-400 text-sm h-10">{{ $plan->description }}</p>
                            
                            <div class="mt-6 mb-8">
                                <span class="text-4xl font-extrabold text-gray-900 dark:text-white">₹{{ number_format($plan->price_monthly) }}</span>
                                <span class="text-gray-500 dark:text-gray-400">/mo</span>
                            </div>

                            <ul class="space-y-4 mb-8 text-sm text-gray-600 dark:text-gray-300">
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    {{ $plan->max_users }} Users
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    {{ $plan->max_branches }} Branches
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 {{ $plan->api_access ? 'text-green-500' : 'text-gray-300 dark:text-gray-600' }} mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $plan->api_access ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path></svg>
                                    API Access
                                </li>
                            </ul>

                            <form action="{{ route('admin.subscription.subscribe') }}" method="POST">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <input type="hidden" name="billing_cycle" value="monthly">
                                <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold transition-colors {{ $currentSubscription && $currentSubscription->plan_id == $plan->id ? 'bg-gray-100 text-gray-800 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white' }}" {{ $currentSubscription && $currentSubscription->plan_id == $plan->id ? 'disabled' : '' }}>
                                    {{ $currentSubscription && $currentSubscription->plan_id == $plan->id ? 'Current Plan' : 'Subscribe Now' }}
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
