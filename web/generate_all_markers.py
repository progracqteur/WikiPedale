import urllib

color_list = ['r', 'g', 'o', 'w', 'd']
output_root = './OpenLayers/img/m_'

for c1 in color_list :
	resource = urllib.urlopen("http://localhost/~marcducobu/Wikipedale/web/marker_generator.php?c1="
				+ c1)
	output = open(output_root + c1 + ".png","wb")
	output.write(resource.read())
	output.close()

	resource = urllib.urlopen("http://localhost/~marcducobu/Wikipedale/web/marker_generator.php?c1="
				+ c1 + "&bg=s")
	output = open(output_root + c1 + "_selected.png","wb")
	output.write(resource.read())
	output.close()

	resource = urllib.urlopen("http://localhost/~marcducobu/Wikipedale/web/marker_generator.php?c1="
				+ c1 + "&bg=l")
	output = open(output_root + c1 + "_no_active.png","wb")
	output.write(resource.read())
	output.close()

	for c2 in color_list :
		resource = urllib.urlopen("http://localhost/~marcducobu/Wikipedale/web/marker_generator.php?c1="
				+ c1 + "&c2=" + c2)
		output = open(output_root + c1 + c2 +".png","wb")
		output.write(resource.read())
		output.close()

		resource = urllib.urlopen("http://localhost/~marcducobu/Wikipedale/web/marker_generator.php?c1="
				+ c1 + "&c2=" + c2 + "&bg=s")
		output = open(output_root + c1 + c2 +"_selected.png","wb")
		output.write(resource.read())
		output.close()

		resource = urllib.urlopen("http://localhost/~marcducobu/Wikipedale/web/marker_generator.php?c1="
				+ c1 + "&c2=" + c2 + "&bg=l")
		output = open(output_root + c1 + c2 +"_no_active.png","wb")
		output.write(resource.read())
		output.close()
		
		for c3 in color_list :
			resource = urllib.urlopen("http://localhost/~marcducobu/Wikipedale/web/marker_generator.php?c1="
				+ c1 + "&c2=" + c2 + "&c3=" + c3)
			output = open(output_root + c1 + c2 + c3 +".png","wb")
			output.write(resource.read())
			output.close()

			resource = urllib.urlopen("http://localhost/~marcducobu/Wikipedale/web/marker_generator.php?c1="
				+ c1 + "&c2=" + c2 + "&c3=" + c3  + "&bg=s")
			output = open(output_root + c1 + c2 + c3 +"_selected.png","wb")
			output.write(resource.read())
			output.close()

			resource = urllib.urlopen("http://localhost/~marcducobu/Wikipedale/web/marker_generator.php?c1="
				+ c1 + "&c2=" + c2 + "&c3=" + c3  + "&bg=l")
			output = open(output_root + c1 + c2 + c3 +"_no_active.png","wb")
			output.write(resource.read())
			output.close()