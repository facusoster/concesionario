<div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-7 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="h4 mb-3">Login</h2>

                <form method="post" action="login_process.php" class="d-grid gap-2">
                    <div>
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control" type="email" id="email" name="email" required autocomplete="username">
                    </div>

                    <div>
                        <label class="form-label" for="password">Password</label>
                        <input class="form-control" type="password" id="password" name="password" required autocomplete="current-password">
                    </div>

                    <div class="mt-2">
                        <button class="btn btn-primary w-100" type="submit">Ingresar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
