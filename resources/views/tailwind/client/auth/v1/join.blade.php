@extends('tailwind.layouts.client')

@section('content')
<div class="max-w-5xl mx-auto px-2" x-data="initData()">
    <div class="p-6 text-gray-800">
        <div
            class="bg-white flex flex-col md:flex-row"
        >
            <div class="flex-1 px-4 md:pl-8 md:pr-16 flex flex-col justify-center items-center md:order-2">
                <template x-if="message">
                    <div class="py-2 px-4 flex justify-center items-center">
                        <div x-text="message" class="text-rose-600"></div>
                    </div>
                </template>
                <template x-if="errors['phone']">
                    <div class="py-2 px-4 flex justify-center items-center">
                        <template x-for="error in errors['phone']">
                            <div x-html="error" class="text-rose-600"></div>
                        </template>
                    </div>
                </template>
                <div class="block w-full mb-2">
                    <label class="block text-gray-500 my-1 font-semibold text-xl text-center">Enter your phone number</label>
                    <input
                        type="number"
                        x-model="phone"
                        class="text-center block w-full px-4 py-3 text-xl text-brand-primary bg-white border border-brand-primary rounded transition ease-in-out focus:bg-white focus:ring-1 ring-brand-primary focus:outline-none font-semibold"
                        placeholder="01XXXXXXXXX"
                        autocomplete="off"
                    />
                </div>
                <template x-if="step === 3">
                    <div class="block w-full mb-2">
                        <label class="block text-gray-500 my-1 font-semibold text-xl text-center">Enter your full name</label>
                        <input
                            type="text"
                            x-model="name"
                            class="text-center block w-full px-4 py-3 text-xl text-brand-primary bg-white border border-brand-primary rounded transition ease-in-out focus:bg-white focus:ring-1 ring-brand-primary focus:outline-none font-semibold"
                            placeholder="Full Name"
                            autocomplete="off"
                        />
                    </div>
                </template>
                <template x-if="step === 2 || step === 3">
                    <div class="block w-full mb-2">
                        <label class="block text-gray-500 my-1 font-semibold text-xl text-center">Enter your password</label>
                        <input
                            type=""
                            x-model="password"
                            class="text-center block w-full px-4 py-3 text-xl text-brand-primary bg-white border border-brand-primary rounded transition ease-in-out focus:bg-white focus:ring-1 ring-brand-primary focus:outline-none font-semibold"
                            placeholder="Password"
                            autocomplete="off"
                        />
                    </div>
                </template>
                <button
                    type="button"
                    @click="submit"
                    :class="{ 
                        'bg-blue-600 hover:bg-blue-700 cursor-pointer shadow': phone.length === 11,
                        'bg-gray-400 cursor-not-allowed': phone.length !== 11
                    }"
                    class="my-4 block w-full py-3  text-white font-medium text-xl leading-snug uppercase rounded focus:outline-none focus:ring-0 transition duration-150 ease-in-out"
                    :disabled="phone.length !== 11"
                >
                    NEXT
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
    const url = `{{ route('v1.join') }}`;
    // const redirect = `{{ $redirect ?? '/' }}`;
    const redirect = window.location;

    function initData() {
        return {
            message: "",
            errors: [],
            loading: true,
            phone: "",
            name: "",
            password: "",
            step: 1,
            submit() {
                //return alert(this.phone);

                this.loading = true;

                axios.post(url, {
                    phone: this.phone,
                    name: this.name,
                    password: this.password,
                    step: this.step,
                },
                {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                })
                .then(({data}) => {
                    console.log(data)

                    this.step = data.step;

                    this.loading = false;

                    if(data.isLogin) {
                        window.location.href = redirect;
                    }
                })
                .catch((error) => {
                    if(error.response) {
                        this.message = error.response.data.message;
                        this.errors = error.response.data.errors
                        
                        setTimeout(() => {
                            this.message = [];
                            this.errors = [];
                        }, 5000);
                    } else {
                        this.message = "Somthing went wrong!";
                    }

                    this.loading = false;

                })
            },
        }
    }
</script>
@endsection