package tools;

import java.util.ArrayList;

//класс реализующий товары
public class Goods {
	public int product_id;
	//Categories	Status	Image	Length	Width	Height	Weight	Manufacturer	Stores	Quantity	Price	Related Products	Description	Meta Title
	
	public Model Model;
	public String Category_id;
	public String Images;
	public String Image_Path;
	public String Goods_id;
	public String Categories;
	public String name;
	public String description;
	public String price;
	public ArrayList<String> photo;

	public String groupStr;

	public String typeStr;
	
	public  Goods() {
		photo = new ArrayList<String>();
	}
	
}
