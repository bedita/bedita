require 'rubygems'
require 'colored'

namespace "bedita" do

desc "checkout bedita-db"
task :checkout_db do
	system("svn co --username #{USER} --password #{PASS} https://svn.channelweb.it/bedita/trunk/bedita-db")
end

desc "checkout bedita-app"
task :checkout_app do
	system("svn co --username #{USER} --password #{PASS} https://svn.channelweb.it/bedita/trunk/bedita-app")
end

desc "checkout cake"
task :checkout_cake do
	system("svn co --username #{USER} --password #{PASS} https://svn.channelweb.it/bedita/trunk/cake")
end

desc "checkout vendors"
task :checkout_vendors do
	system("svn co --username #{USER} --password #{PASS} https://svn.channelweb.it/bedita/trunk/vendors")
end

desc "checkout cake.sh"
task :checkout_shell do
	system("svn export --username #{USER} --password #{PASS} https://svn.channelweb.it/bedita/trunk/cake.sh")
end

desc "mkdir apache"
directory "apache"

desc "mkdir media"
directory "media/imgcache"

desc "rename *.sample"
task :rename_configs do
	system("rename 's/\.sample$//' bedita-app/config/*.sample")
	system("rename 's/\.sample$//' bedita-app/webroot/*.sample")
end



desc "checkout complete bedita environment"
task :init => [:checkout_db, :checkout_app, :checkout_cake, :checkout_vendors, :checkout_shell, :media, :apache, :rename_configs]

desc "update complete bedita env"
task :update do
	p "bedita-app"; system("cd bedita-app; svn --username #{USER} --password #{PASS} up")
	p "bedita-db"; system("cd bedita-db; svn --username #{USER} --password #{PASS} up")
	p "cake"; system("cd cake; svn --username #{USER} --password #{PASS} up")
	p "vendors"; system("cd vendors; svn --username #{USER} --password #{PASS} up")
end


end

