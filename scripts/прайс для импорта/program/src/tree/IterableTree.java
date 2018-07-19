package tree;

import java.util.ArrayList;
import java.util.Iterator;

import com.sun.org.apache.xml.internal.dtm.ref.DTMDefaultBaseIterators.DescendantIterator;

public class IterableTree<V> extends NodeTree<V> {
	Iterator iter;
	/** устанавливает тип итератора
	 * @param type -"asc" - ascent        "desc" = descent  "full" - full
	 * @param n
	 */
	public void SetIterator(String type, Node n) {
		if (type.equals("asc")) {
			AscendIterator asc = new AscendIterator(n);
			iter = asc;
		}
		if (type.equals("desc")) {
			DescendIterator desc  = new DescendIterator();
			desc.setN(n);
			iter = desc;
		}
		if (type.equals("full")) {
			FullIterator  desc  = new FullIterator();
			
			iter = desc;
		}
		
	}
	
	
	/** итератор по возрастанию
	 * @author Клим
	 *
	 */
	class AscendIterator implements Iterator{
		private int size;
		private ArrayList<Node> temparray;
		private Node current;
		//private Node n;
		private ArrayList<Node> stack;


		public AscendIterator(Node n) {
			temparray = new ArrayList<Node>();
			stack = new ArrayList<Node>();
			//this.n = n;
			this.current = n;
			temparray.add(n);
		}
		@Override
		public boolean hasNext() {
			if (current==null) {//закончились все возможные варианты
				return false;
			}
			return true;
			
		}

		@Override
		public Node next() {
			if (current == null) {
				return null;
			}
			Node ret = current;
			stack.add(current);
			current = null;
			if (ret.hasParents()) {
				temparray.addAll(ret.getParent());
				
			}
			Iterator<Node>  it = temparray.iterator();
			while (it.hasNext()) {
				Node d = it.next();
				if (stack.indexOf(d)==-1) {
					current = d;
					temparray.remove(d);
					break;
				}
				else {
					temparray.remove(d);
				}
			}
			
			return ret;
		}
		
	}
	
	/** итератор по убыванию
	 * @author Клим
	 *
	 */
	class DescendIterator implements Iterator{
		Node current;
		public Node n;
		
		public void setN(Node n) {
			this.n = n;
			this.current = n;
		}
		@Override
		public boolean hasNext() {
			// TODO Auto-generated method stub
			return false;
		}

		@Override
		public Node next() {
			// TODO Auto-generated method stub
			return null;
		}
		
	}
	
	/** полный обход всего списка
	 * @author Клим
	 *
	 */
	class FullIterator implements Iterator{
		int index = 0;
		@Override
		public boolean hasNext() {
			// TODO Auto-generated method stub
			return false;
		}

		@Override
		public Node next() {
			// TODO Auto-generated method stub
			return null;
		}
		
	}
	@Override
	public Iterator iterator() {
		// TODO Auto-generated method stub
		return iter;
	}

}
