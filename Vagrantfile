# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.

site_dir = "/var/www/shop-cart"

Vagrant.configure("2") do |config|
  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://atlas.hashicorp.com/search.
  config.vm.box = "Shop-cart"
  config.vm.box_url = "http://opscode-vm-bento.s3.amazonaws.com/vagrant/virtualbox/opscode_centos-7.2_chef-provisionerless.box"
#  config.vm.hostname = "image-server.local"

  # Disable automatic box update checking. If you disable this, then
  # boxes will only be checked for updates when the user runs
  # `vagrant box outdated`. This is not recommended.
  # config.vm.box_check_update = false

  config.ssh.insert_key = false

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  # config.vm.network "forwarded_port", guest: 80, host: 8080

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  config.vm.network "private_network", ip: "192.168.33.10"

  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
  # config.vm.network "public_network"

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  # config.vm.synced_folder "../data", "/vagrant_data"
  config.vm.synced_folder ".", site_dir

  # Provider-specific configuration so you can fine-tune various
  # backing providers for Vagrant. These expose provider-specific options.
  # Example for VirtualBox:
  #
  config.vm.provider "virtualbox" do |vb|
    # Display the VirtualBox GUI when booting the machine
    # vb.gui = true
    # Customize the amount of memory on the VM:
    vb.memory = "4096"
    vb.cpus = 2
#    vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
#    vb.customize ["modifyvm", :id, "--natdnsproxy1", "on"]
#    vb.customize ["modifyvm", :id, "--nictype1", "virtio"]
  end
  #
  # View the documentation for the provider you are using for more
  # information on available options.

  # Define a Vagrant Push strategy for pushing to Atlas. Other push strategies
  # such as FTP and Heroku are also available. See the documentation at
  # https://docs.vagrantup.com/v2/push/atlas.html for more information.
  # config.push.define "atlas" do |push|
  #   push.app = "YOUR_ATLAS_USERNAME/YOUR_APPLICATION_NAME"
  # end

  # Enable provisioning with a shell script. Additional provisioners such as
  # Puppet, Chef, Ansible, Salt, and Docker are also available. Please see the
  # documentation for more information about their specific syntax and use.
  config.vm.provision "shell", args: site_dir, inline: <<-SHELL
    SITE_DIR="${1}"

    chown -R 1000:1000 ${SITE_DIR}/../
    adduser -l -o -m -u 1000 -U -d /var/lib/nginx nginx

    yum -y install epel-release.noarch
    yum -y update
    yum -y localinstall https://mirror.webtatic.com/yum/el7/webtatic-release.rpm
    yum -y update
    yum -y install htop openssl-devel mc screen php56w php56w-devel php56w-mbstring php56w-process php56w-bcmath php56w-fpm git composer nginx
    yum -y groupinstall 'Development Tools'

    cd ${SITE_DIR}/config/vagrant/etc
    cp -Rf ./* /etc
    chown -R root:vagrant /var/lib/php/*

    systemctl enable php-fpm
    systemctl restart php-fpm

    systemctl enable nginx
    systemctl restart nginx
  SHELL
  
  config.vm.provision "shell", privileged: false, args: site_dir, inline: <<-SHELL
    if [ ! -d ~/.composer ]; then
      mkdir ~/.composer
    fi
  SHELL

  config.vm.provision "file", source: "~/.ssh/id_rsa.pub", destination: "~/.ssh/id_rsa.pub"
  config.vm.provision "file", source: "~/.ssh/id_rsa", destination: "~/.ssh/id_rsa"
  config.vm.provision "file", source: "~/.gitconfig", destination: "~/.gitconfig"
  config.vm.provision "file", source: "~/.composer/auth.json", destination: "~/.composer/auth.json"

  config.vm.provision "shell", privileged: false, args: site_dir, inline: <<-SHELL
    HOST_KEY=`cat ~/.ssh/id_rsa.pub`
    AUTH=`cat ~/.ssh/authorized_keys | grep "${HOST_KEY}"`

    chmod -R 600 ~/.ssh/*

    if [ -z "${AUTH}" ]; then
      echo "${HOST_KEY}" >> ~/.ssh/authorized_keys
    fi

    SITE_DIR="${1}"
    cd ${SITE_DIR}

    if [ -z "`cat ~/.bashrc | grep PATH`" ]; then
      echo 'PATH=$PATH:$HOME/.composer/vendor/bin' >> ~/.bashrc
      echo 'export PATH' >> ~/.bashrc
    fi

    composer install
  SHELL
end
