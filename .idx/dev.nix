{ pkgs, ... }: {
  channel = "stable-23.11";
  packages = [
    pkgs.php82
    pkgs.php82Packages.composer
    pkgs.nodejs_20
    pkgs.git-lfs
    # Add Python packages for the AI microservice
    pkgs.python311
    pkgs.python311Packages.pip
    pkgs.python311Packages.virtualenv
  ];
  env = {};
  idx = {
    extensions = [
      "bmewburn.vscode-intelephense-client" # PHP autocomplete
      "ms-python.python"                    # Python support
      "vue.volar"                           # Frontend support
    ];
    previews = {
      enable = true;
      previews = {
        web = {
          command = ["php" "artisan" "serve" "--port" "$PORT" "--host" "0.0.0.0"];
          manager = "web";
        };
      };
    };
  };
}