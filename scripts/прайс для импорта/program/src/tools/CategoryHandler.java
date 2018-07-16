package tools;

import java.util.ArrayList;

import org.openqa.selenium.Rotatable;

public class CategoryHandler {
	public ArrayList<Category> ilist;
	public CategoryHandler() {
		ilist = new ArrayList<Category>();
		Category rootcat = new Category();
		rootcat.Category_name = "root";
		rootcat.id = "0";
		ilist.add(rootcat);
	}
	/** adds raw data and converts to object model
	 * @param arr
	 * @param id
	 */
	public void add(String[] arr, String id ) {
		int len  = arr.length;
		Category tempcat = this.CheckIfExists(arr[len-1]);
		tempcat.id = id;
		tempcat.Cat_Level = len;
		//указываем родителя, если есть
		if (len==1) {//первый уровень - родитель root
			tempcat.ParentCategory = this.CheckIfExists("root");
		}
		else if (len !=1) {
			tempcat.ParentCategory = this.CheckIfExists(arr[len-2]);
			this.add(this.CheckIfExists(arr[len-2]));
		}
		this.add(tempcat);
		
	}
	private void add(Category tempcat) {
		for (Category category : ilist) {
			if (category.Category_name.equals(tempcat.Category_name)){
				ilist.remove(category);
				ilist.add(tempcat);
				return;
			}
		}
		ilist.add(tempcat);
		
		// TODO Auto-generated method stub
		
	}
	/** checks whether this object exists, if not, creates this object and assigns Category_name to it
	 * @param name
	 * @return
	 */
	public Category CheckIfExists(String name) {
		for (Category category : ilist) {
			if(category.Category_name.equals(name)) {
				return category;
			}
		}
		Category cat = new Category();
		cat.Category_name = name;
		return cat; 
	}
	
}
