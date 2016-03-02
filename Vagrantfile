# -*- mode: ruby -*-
# vi: set ft=ruby :

hostname = "bedita4"
name = "BEdita 4"

cpus = 2
memory = 2048

Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/trusty64"

  # Set up machine network and hostname:
  config.vm.hostname = "#{hostname}.bedita.local"
  config.vm.network "private_network", ip: "10.0.83.4"

  # Add additional shared folder to easen Apache setup:
  config.vm.synced_folder ".", "/var/www/html"
  config.vm.synced_folder "provision/", "/vagrant"

  # Configure VirtualBox provider:
  config.vm.provider "virtualbox" do |vb|
    vb.name = name
    vb.customize [
      "modifyvm", :id,
      "--groups", "/Vagrant"
    ]

    vb.cpus = cpus
    vb.memory = memory
  end

  # Install Ansible via PIP and Git via APT:
  config.vm.provision "shell", path: "provision/install_ansible.sh"

  # Workaround for mitchellh/vagrant#6793 (see https://github.com/mitchellh/vagrant/issues/6793#issuecomment-172408346):
  config.vm.provision "shell" do |s|
    s.inline = '[[ ! -f $1 ]] || grep -F -q "$2" $1 || sed -i "/__main__/a \\    $2" $1'
    s.args = ['/usr/local/bin/ansible-galaxy', "if sys.argv == ['/usr/local/bin/ansible-galaxy', '--help']: sys.argv.insert(1, 'info')"]
  end

  # Ansible provisioning:
  config.vm.provision :ansible_local do |ansible|
    ansible.galaxy_role_file = "requirements.yml"
    ansible.galaxy_roles_path = "roles"

    ansible.playbook = "playbook.yml"

    if File.exists?("vars/vagrant.yml")
      ansible.extra_vars = "vars/vagrant.yml"
    end

    ansible.skip_tags = ["letsencrypt", "letsencrypt-certificates"]
  end
end
