package tree;

import java.util.Iterator;

import com.sun.org.apache.xml.internal.dtm.ref.DTMDefaultBaseIterators.DescendantIterator;

public class IterableTree<V> extends NodeTree<V> {
	Iterator iter;
	public void SetIterator(String type, Node n) {
		if (type.equals("asc")) {
			AscendIterator asc = new AscendIterator();
			asc.setN(n);
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
		private int index = 0;
		private Node current;
		private Node n;
		public void setN(Node n) {
			this.n = n;
			this.current = n;
		}

		
		@Override
		public boolean hasNext() {
			if (index == size) {
				
			}
			if (index == 0) {
			size = current.getParent().size();
			if (size == 0) {
				return false;
			}
			
			}
			return true;
			
		}

		@Override
		public Node next() {
			 			
			return current;
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
