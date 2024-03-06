@extends('tailwind.layouts.client')

@section('content')
<div class="max-w-6xl mx-auto px-2 z-0" x-data="initData()">
    <div class="p-6 text-gray-800">
        <div
            class="bg-white flex flex-col md:flex-row"
        >
            <div class="flex-1 px-4 md:pl-8 md:pr-16 flex flex-col justify-center items-center md:order-2">
                <template x-if="message">
                    <div class="py-4 px-4 flex justify-center items-center">
                        <div x-text="message" class="text-orange-600 md:text-lg"></div>
                    </div>
                </template>
                <div class="block w-full mb-4 relative">
                    <input
                        type="number"
                        x-model="phone"
                        :class="step === 2 ? 'border-green-700/10 text-green-700' : 'border-brand-primary text-brand-primary'"
                        class="text-center block w-full px-4 py-3 text-2xl bg-white border rounded transition ease-in-out focus:bg-white focus:ring-1 ring-brand-primary focus:outline-none font-semibold"
                        placeholder="01XXXXXXXXX"
                        autocomplete="off"
                    />
                    <template x-if="step === 2">
                        <div class="absolute inset-0 bg-green-700/10 z-50 flex items-center justify-end px-4">
                            <span class="text-green-700 text-2xl">&#10003;</span>
                        </div>
                    </template>
                </div>
                <template x-if="step === 2 && !sms">
                    <div class="block w-full mb-4 relative">
                        <input
                            type="text"
                            x-model="name"
                            :class="step === 2 && hasName ? 'border-green-700/10 text-green-700' : 'border-brand-primary text-brand-primary'"
                            class="text-center block w-full px-4 py-3 text-2xl bg-white border rounded transition ease-in-out focus:bg-white focus:ring-1 ring-brand-primary focus:outline-none font-semibold"
                            placeholder="Full Name"
                            autocomplete="off"
                        />
                        <template x-if="step === 2 && hasName">
                            <div class="absolute inset-0 bg-green-700/10 z-50 flex items-center justify-end px-4">
                                <span class="text-green-700 text-2xl">&#10003;</span>
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="step === 2 && !sms">
                    <div class="block w-full mb-4">
                        <input
                            :type="password.length ? 'password' : 'text'"
                            x-model="password"
                            class="text-center block w-full px-4 py-3 text-2xl text-brand-primary bg-white border border-brand-primary rounded transition ease-in-out focus:bg-white focus:ring-1 ring-brand-primary focus:outline-none font-semibold"
                            placeholder="Password"
                            autocomplete="off"
                        />
                    </div>
                </template>
                <template x-if="!send">
                    <div class="w-full">
                        <div class="w-full mb-4 flex justify-start items-center">
                            <label class="flex items-center gap-2 text-lg md:text-xl">
                                <input type="checkbox" x-model="sms" class="w-5 h-5" />
                                I don't remember my password
                            </label>
                        </div>
                        <template x-if="sms">
                            <div class="w-full mb-4 flex justify-start items-center">
                                <label class="flex items-start gap-2 text-lg md:text-xl text-brand-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Forgot your password? No problem. Just let us know your phone number and we will sms you.</span>
                                </label>
                            </div>
                        </template>
                    </div>
                </template>
                <button
                    type="button"
                    @click="submit"
                    :class="{ 
                        'bg-blue-600 hover:bg-blue-700 cursor-pointer shadow': !(loading || (step === 1 && phone.length !== 11) || (step === 2 && !sms && (!name.length || !password.length))),
                        'bg-blue-600/60 cursor-not-allowed': loading || (step === 1 && phone.length !== 11) || (step === 2 && !sms && (!name.length || !password.length))
                    }"
                    class="flex justify-center items-center gap-4 w-full h-14 text-white font-medium text-2xl leading-snug rounded focus:outline-none focus:ring-0 transition duration-150 ease-in-out"
                    :disabled="loading || (step === 1 && phone.length !== 11) || (step === 2 && !sms && (!name.length || !password.length))"
                >
                    <template x-if="loading">
                        <div class="flex gap-2 justify-center items-center">
                            <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-50" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="">Processing ...</span>
                        </div>
                    </template>
                    <template x-if="!loading">
                        <div class="uppercase" x-text="sms ? 'Send Password' : 'NEXT'"></div>
                    </template>
                </button>
            </div>
            <div
                class="flex-1 p-4 md:pl-8 md:order-1"
            >
                <img
                    src="{{ asset('images/login-animate.gif') }}"
                    class="w-full"
                    alt="Sample image"
                />
            </div>
        </div>
    </div>
</div>

<script>
    const joinUrl = `{{ $join_url }}`;
    const loginUrl = `{{ $login_url }}`;

    function initData() {
        return {
            message: "",
            phone: "",
            name: "",
            password: "",
            loading: false,
            sms: false,
            send: false,
            hasName: false,
            step: 1,
            submit() {
                this.loading = true;
                this.message = "";

                if(this.sms) {
                    this.step = 1;
                }

                if(this.step === 1) {
                    setTimeout(() => {
                        return this.callJoinApi();
                    }, 1000);
                }

                if(this.step === 2) {
                    setTimeout(() => {
                        return this.callLoginApi();
                    }, 1000);
                }
            },
            callJoinApi() {
                axios.post(joinUrl, {
                    phone: this.phone,
                    sms: this.sms,
                }, 
                {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(({data}) => {
                    console.log(data);

                    this.message = data.message;
                    this.phone = data.phone;
                    this.name = data.name;
                    this.step = data.step;
                    this.send = data.send;
                    this.sms = false;
                    this.hasName = data.hasName;

                    this.loading = false;
                })
                .catch((error) => {
                    console.log(error);

                    if(error.response) {
                        this.message = error.response.data.message;    
                    }

                    this.loading = false;
                })
            },
            callLoginApi() {
                axios.post(loginUrl, {
                    bmdc_no: this.phone,
                    name: this.name,
                    password: this.password,
                    login_type: 'mobile_number',
                }, 
                {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(({data}) => {
                    console.log(data);

                    this.message = data.message;

                    if(data.token) {
                        return this.setAuth(data.token)
                            .then(({data}) => {
                                console.log(data);
                                window.location.reload();
                            })
                            .catch((error) => {
                                console.log(error);
                            });
                    }

                    this.loading = false;
                })
                .catch((error) => {
                    console.log(error);

                    if(error.response) {
                        this.message = error.response.data.message;    
                    }

                    this.loading = false;
                })
            },
            async setAuth(token) {
                return await axios.post('/authentication-by-token', {
                    token: token,
                });
            }
        }
    }
</script>
@endsection